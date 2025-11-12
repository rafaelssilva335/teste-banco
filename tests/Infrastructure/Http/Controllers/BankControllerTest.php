<?php

namespace Tests\Infrastructure\Http\Controllers;

use Core\Application\UseCases\DepositUseCase;
use Core\Application\UseCases\WithdrawUseCase;
use Core\Application\UseCases\TransferUseCase;
use Core\Application\UseCases\GetBalanceUseCase;
use Core\Application\UseCases\ResetUseCase;
use Core\Domain\Entities\Account;
use Core\Domain\Repositories\AccountRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @covers \Infrastructure\Http\Controllers\BankController
 */
class BankControllerTest extends TestCase
{
    private AccountRepository|MockObject $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = $this->createMock(AccountRepository::class);
        
        $this->app->singleton(\Core\Domain\Repositories\AccountRepository::class, function () {
            return $this->mockRepository;
        });
        
        $this->app->singleton(\Core\Application\UseCases\ResetUseCase::class, function () {
            return new \Core\Application\UseCases\ResetUseCase($this->mockRepository);
        });
        
        $this->app->singleton(\Core\Application\UseCases\GetBalanceUseCase::class, function () {
            return new \Core\Application\UseCases\GetBalanceUseCase($this->mockRepository);
        });
        
        $this->app->singleton(\Core\Application\UseCases\DepositUseCase::class, function () {
            return new \Core\Application\UseCases\DepositUseCase($this->mockRepository);
        });
        
        $this->app->singleton(\Core\Application\UseCases\WithdrawUseCase::class, function () {
            return new \Core\Application\UseCases\WithdrawUseCase($this->mockRepository);
        });
        
        $this->app->singleton(\Core\Application\UseCases\TransferUseCase::class, function () {
            return new \Core\Application\UseCases\TransferUseCase($this->mockRepository);
        });
    }

    /**
     * Reset state before starting tests
     * POST /reset
     * Expected: 200 OK
     */
    public function test_reset_endpoint_returns_ok(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('clear');
        
        $this->post('/reset');
        
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getContent());
    }
    /**
     * Get balance for non-existing account
     * GET /balance?account_id=1234
     * Expected: 404 0
     */
    public function test_get_balance_for_non_existing_account_returns_404(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('1234')
            ->willReturn(null);
        
        $this->get('/balance?account_id=1234');
        
        $this->assertEquals(404, $this->response->getStatusCode());
        $this->assertEquals('0', $this->response->getContent());
    }

    /**
     * Create account with initial balance
     * POST /event {"type":"deposit", "destination":"100", "amount":10}
     * Expected: 201 {"destination": {"id":"100", "balance":10}}
     */
    public function test_deposit_creates_account_with_initial_balance(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('100')
            ->willReturn(null);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Account $a) => 
                $a->getId() === '100' && $a->getBalance() === 10.0
            ));
        
        $event = [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(201, $this->response->getStatusCode());
        
        $response = json_decode($this->response->getContent(), true);
        
        $this->assertArrayHasKey('destination', $response);
        $this->assertEquals('100', $response['destination']['id']);
        $this->assertEquals(10, $response['destination']['balance']);
    }

    /**
     * Deposit into existing account
     * POST /event {"type":"deposit", "destination":"100", "amount":10}
     * Expected: 201 {"destination": {"id":"100", "balance":20}}
     */
    public function test_deposit_into_existing_account_accumulates_balance(): void
    {
        $account = new Account('100', 10.0);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('100')
            ->willReturn($account);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Account $a) => 
                $a->getId() === '100' && $a->getBalance() === 20.0
            ));
        
        $event = [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(201, $this->response->getStatusCode());
        
        $response = json_decode($this->response->getContent(), true);
        
        $this->assertArrayHasKey('destination', $response);
        $this->assertEquals('100', $response['destination']['id']);
        $this->assertEquals(20, $response['destination']['balance']);
    }

    /**
     * Get balance for existing account
     * GET /balance?account_id=100
     * Expected: 200 20
     */
    public function test_get_balance_for_existing_account_returns_balance(): void
    {
        $account = new Account('100', 20.0);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('100')
            ->willReturn($account);
        
        $this->get('/balance?account_id=100');
        
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('20', $this->response->getContent());
    }

    /**
     * Withdraw from non-existing account
     * POST /event {"type":"withdraw", "origin":"200", "amount":10}
     * Expected: 404 0
     */
    public function test_withdraw_from_non_existing_account_returns_404(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('200')
            ->willReturn(null);
        
        $event = [
            'type' => 'withdraw',
            'origin' => '200',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(404, $this->response->getStatusCode());
        $this->assertEquals('0', $this->response->getContent());
    }

    /**
     * Withdraw from existing account
     * POST /event {"type":"withdraw", "origin":"100", "amount":5}
     * Expected: 201 {"origin": {"id":"100", "balance":15}}
     */
    public function test_withdraw_from_existing_account_updates_balance(): void
    {
        $account = new Account('100', 20.0);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('100')
            ->willReturn($account);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Account $a) => 
                $a->getId() === '100' && $a->getBalance() === 15.0
            ));
        
        $event = [
            'type' => 'withdraw',
            'origin' => '100',
            'amount' => 5
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(201, $this->response->getStatusCode());
        
        $response = json_decode($this->response->getContent(), true);
        
        $this->assertArrayHasKey('origin', $response);
        $this->assertEquals('100', $response['origin']['id']);
        $this->assertEquals(15, $response['origin']['balance']);
    }

    /**
     * Transfer from existing account
     * POST /event {"type":"transfer", "origin":"100", "amount":15, "destination":"300"}
     * Expected: 201 {"origin": {"id":"100", "balance":0}, "destination": {"id":"300", "balance":15}}
     */
    public function test_transfer_from_existing_account_creates_destination(): void
    {
        $originAccount = new Account('100', 15.0);
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(function ($id) use ($originAccount) {
                if ($id === '100') {
                    return $originAccount;
                }
                if ($id === '300') {
                    return null; // Destination doesn't exist yet
                }
                return null;
            });
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('save');
        
        $event = [
            'type' => 'transfer',
            'origin' => '100',
            'destination' => '300',
            'amount' => 15
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(201, $this->response->getStatusCode());
        
        $response = json_decode($this->response->getContent(), true);
        
        $this->assertArrayHasKey('origin', $response);
        $this->assertArrayHasKey('destination', $response);
        $this->assertEquals('100', $response['origin']['id']);
        $this->assertEquals(0, $response['origin']['balance']);
        $this->assertEquals('300', $response['destination']['id']);
        $this->assertEquals(15, $response['destination']['balance']);
    }

    /**
     * Transfer from non-existing account
     * POST /event {"type":"transfer", "origin":"200", "amount":15, "destination":"300"}
     * Expected: 404 0
     */
    public function test_transfer_from_non_existing_account_returns_404(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('200')
            ->willReturn(null);
        
        $event = [
            'type' => 'transfer',
            'origin' => '200',
            'destination' => '300',
            'amount' => 15
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(404, $this->response->getStatusCode());
        $this->assertEquals('0', $this->response->getContent());
    }

    /**
     * Invalid event type
     * POST /event {"type":"invalid"}
     * Expected: 400 Bad Request
     */
    public function test_invalid_event_type_returns_400(): void
    {
        $event = [
            'type' => 'invalid'
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Missing required fields for deposit
     * POST /event {"type":"deposit"}
     * Expected: 400 Bad Request
     */
    public function test_deposit_without_destination_returns_400(): void
    {
        $event = [
            'type' => 'deposit',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Missing required fields for withdraw
     * POST /event {"type":"withdraw"}
     * Expected: 400 Bad Request
     */
    public function test_withdraw_without_origin_returns_400(): void
    {
        $event = [
            'type' => 'withdraw',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Missing required fields for transfer
     * POST /event {"type":"transfer"}
     * Expected: 400 Bad Request
     */
    public function test_transfer_without_required_fields_returns_400(): void
    {
        $event = [
            'type' => 'transfer',
            'amount' => 10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Missing amount field
     * POST /event {"type":"deposit", "destination":"100"}
     * Expected: 400 Bad Request
     */
    public function test_deposit_without_amount_returns_400(): void
    {
        $event = [
            'type' => 'deposit',
            'destination' => '100'
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Invalid amount (negative)
     * POST /event {"type":"deposit", "destination":"100", "amount":-10}
     * Expected: 400 Bad Request (via domain validation)
     */
    public function test_deposit_with_negative_amount_returns_400(): void
    {
        $event = [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => -10
        ];
        
        $this->json('POST', '/event', $event);
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }

    /**
     * Missing account_id in balance query
     * GET /balance
     * Expected: 400 Bad Request
     */
    public function test_get_balance_without_account_id_returns_400(): void
    {
        $this->get('/balance');
        
        $this->assertEquals(400, $this->response->getStatusCode());
    }
}


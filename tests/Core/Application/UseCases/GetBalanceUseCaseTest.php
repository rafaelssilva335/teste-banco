<?php

namespace Tests\Core\Application\UseCases;

use Core\Application\UseCases\GetBalanceUseCase;
use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;
use Tests\TestCase;

class GetBalanceUseCaseTest extends TestCase
{
    private GetBalanceUseCase $useCase;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = $this->createMock(AccountRepository::class);
        $this->useCase = new GetBalanceUseCase($this->mockRepository);
    }

    public function test_can_get_balance_from_account()
    {
        $account = new Account('123', 100.0);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('123')
            ->willReturn($account);
        
        $balance = $this->useCase->execute('123');
        
        $this->assertEquals(100.0, $balance);
    }

    public function test_throws_exception_when_account_not_found()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('999')
            ->willReturn(null);
        
        $this->expectException(AccountNotFoundException::class);
        
        $this->useCase->execute('999');
    }
}


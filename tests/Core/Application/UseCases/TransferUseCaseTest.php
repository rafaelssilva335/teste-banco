<?php

namespace Tests\Core\Application\UseCases;

use Core\Application\UseCases\TransferUseCase;
use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;
use Tests\TestCase;

class TransferUseCaseTest extends TestCase
{
    private TransferUseCase $useCase;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = $this->createMock(AccountRepository::class);
        $this->useCase = new TransferUseCase($this->mockRepository);
    }

    public function test_can_transfer_between_accounts()
    {
        $originAccount = new Account('123', 100.0);
        $destinationAccount = new Account('456', 50.0);
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(function ($id) use ($originAccount, $destinationAccount) {
                return $id === '123' ? $originAccount : $destinationAccount;
            });
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('save');
        
        $result = $this->useCase->execute('123', '456', 30.0);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('origin', $result);
        $this->assertArrayHasKey('destination', $result);
        $this->assertEquals(70.0, $result['origin']->getBalance());
        $this->assertEquals(80.0, $result['destination']->getBalance());
    }

    public function test_creates_destination_account_if_not_exists()
    {
        $originAccount = new Account('123', 100.0);
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(function ($id) use ($originAccount) {
                return $id === '123' ? $originAccount : null;
            });
        
        $this->mockRepository
            ->expects($this->exactly(2))
            ->method('save');
        
        $result = $this->useCase->execute('123', '456', 30.0);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('origin', $result);
        $this->assertArrayHasKey('destination', $result);
        $this->assertEquals(70.0, $result['origin']->getBalance());
    }

    public function test_throws_exception_when_origin_account_not_found()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('999')
            ->willReturn(null);
        
        $this->expectException(AccountNotFoundException::class);
        
        $this->useCase->execute('999', '456', 30.0);
    }
}


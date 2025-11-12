<?php

namespace Tests\Core\Application\UseCases;

use Core\Application\UseCases\WithdrawUseCase;
use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;
use Tests\TestCase;

class WithdrawUseCaseTest extends TestCase
{
    private WithdrawUseCase $useCase;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = $this->createMock(AccountRepository::class);
        $this->useCase = new WithdrawUseCase($this->mockRepository);
    }

    public function test_can_withdraw_from_account()
    {
        $account = new Account('123', 100.0);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('123')
            ->willReturn($account);
        
        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedAccount) {
                return $savedAccount->getBalance() === 70.0;
            }));
        
        $result = $this->useCase->execute('123', 30.0);
        
        $this->assertEquals(70.0, $result->getBalance());
    }

    public function test_throws_exception_when_account_not_found()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with('999')
            ->willReturn(null);
        
        $this->expectException(AccountNotFoundException::class);
        
        $this->useCase->execute('999', 50.0);
    }
}


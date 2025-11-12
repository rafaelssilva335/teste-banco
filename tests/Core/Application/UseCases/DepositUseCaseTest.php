<?php

namespace Tests\Core\Application\UseCases;

use Core\Application\UseCases\DepositUseCase;
use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\InvalidAmountException;
use Core\Domain\Repositories\AccountRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @covers \Core\Application\UseCases\DepositUseCase
 */
class DepositUseCaseTest extends TestCase
{
    private DepositUseCase $useCase;
    private AccountRepository|MockObject $accountRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->useCase = new DepositUseCase($this->accountRepositoryMock);
    }

    public function test_can_deposit_to_existing_account(): void
    {
        $account = new Account('123', 100.0);
        
        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with('123')
            ->willReturn($account);
        
        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Account $a) => $a->getBalance() === 150.0));
        
        $result = $this->useCase->execute('123', 50.0);
        
        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals(150.0, $result->getBalance());
    }

    public function test_can_deposit_to_new_account(): void
    {
        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with('123')
            ->willReturn(null);
        
        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Account $a) => 
                $a->getId() === '123' && $a->getBalance() === 50.0
            ));
        
        $result = $this->useCase->execute('123', 50.0);
        
        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals('123', $result->getId());
        $this->assertEquals(50.0, $result->getBalance());
    }

    public function test_throws_exception_for_invalid_amount(): void
    {
        $this->expectException(InvalidAmountException::class);
        $this->useCase->execute('123', -10);
    }
}


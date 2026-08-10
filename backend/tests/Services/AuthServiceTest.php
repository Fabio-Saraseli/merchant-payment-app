<?php

use App\Models\ApiToken;
use App\Models\Merchant;
use App\Repositories\ApiTokenRepositoryInterface;
use App\Repositories\MerchantRepositoryInterface;
use App\Services\AuthService;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $merchantRepository;
    private $apiTokenRepository;
    private $authService;
    private $merchant;

    protected function setUp(): void
    {
        $this->merchantRepository = $this->createMock(
            MerchantRepositoryInterface::class
        );

        $this->apiTokenRepository = $this->createMock(
            ApiTokenRepositoryInterface::class
        );

        $this->authService = new AuthService(
            $this->merchantRepository,
            $this->apiTokenRepository
        );

        $this->merchant = new Merchant(
            'merchant-1',
            'Demo Merchant',
            'demo@merchant.test',
            password_hash('password123', PASSWORD_DEFAULT),
            'fake_stripe',
            [
                'account_id' => 'fake_account_demo',
            ]
        );
    }

    public function testLoginReturnsTokenForValidCredentials()
    {
        $this->merchantRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('demo@merchant.test')
            ->willReturn($this->merchant);

        $this->apiTokenRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($apiToken) {
                    return
                        $apiToken->getMerchantId() === 'merchant-1'
                        && strlen($apiToken->getTokenHash()) === 64
                        && strtotime($apiToken->getExpiresAt()) > time();
                })
            );

        $result = $this->authService->login(
            'demo@merchant.test',
            'password123'
        );

        $this->assertNotNull($result);
        $this->assertSame(
            $this->merchant,
            $result['merchant']
        );

        $this->assertSame(
            64,
            strlen($result['token'])
        );

        $this->assertNotEmpty(
            $result['expires_at']
        );

        $this->assertGreaterThan(
            time(),
            strtotime($result['expires_at'])
        );
    }

    public function testLoginFailsWhenMerchantDoesNotExist()
    {
        $this->merchantRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('missing@merchant.test')
            ->willReturn(null);

        $this->apiTokenRepository
            ->expects($this->never())
            ->method('create');

        $result = $this->authService->login(
            'missing@merchant.test',
            'password123'
        );

        $this->assertNull($result);
    }

    public function testLoginFailsWithWrongPassword()
    {
        $this->merchantRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($this->merchant);

        $this->apiTokenRepository
            ->expects($this->never())
            ->method('create');

        $result = $this->authService->login(
            'demo@merchant.test',
            'wrong-password'
        );

        $this->assertNull($result);
    }

    public function testValidTokenReturnsMerchant()
    {
        $plainToken = 'valid-test-token';

        $apiToken = new ApiToken(
            'token-1',
            'merchant-1',
            hash('sha256', $plainToken),
            gmdate(
                'Y-m-d H:i:s',
                time() + 3600
            )
        );

        $this->apiTokenRepository
            ->expects($this->once())
            ->method('findByTokenHash')
            ->with(hash('sha256', $plainToken))
            ->willReturn($apiToken);

        $this->merchantRepository
            ->expects($this->once())
            ->method('findById')
            ->with('merchant-1')
            ->willReturn($this->merchant);

        $result = $this->authService->authenticateToken(
            $plainToken
        );

        $this->assertSame(
            $this->merchant,
            $result
        );
    }

    public function testExpiredTokenIsRejected()
    {
        $plainToken = 'expired-test-token';

        $apiToken = new ApiToken(
            'token-1',
            'merchant-1',
            hash('sha256', $plainToken),
            gmdate(
                'Y-m-d H:i:s',
                time() - 3600
            )
        );

        $this->apiTokenRepository
            ->method('findByTokenHash')
            ->willReturn($apiToken);

        $this->merchantRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->authService->authenticateToken(
            $plainToken
        );

        $this->assertNull($result);
    }

    public function testUnknownTokenIsRejected()
    {
        $plainToken = 'unknown-test-token';

        $this->apiTokenRepository
            ->expects($this->once())
            ->method('findByTokenHash')
            ->with(hash('sha256', $plainToken))
            ->willReturn(null);

        $this->merchantRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->authService->authenticateToken(
            $plainToken
        );

        $this->assertNull($result);
    }

    public function testEmptyTokenIsRejected()
    {
        $this->apiTokenRepository
            ->expects($this->never())
            ->method('findByTokenHash');

        $this->merchantRepository
            ->expects($this->never())
            ->method('findById');

        $result = $this->authService->authenticateToken('');

        $this->assertNull($result);
    }
}

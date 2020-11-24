# Reset Password API Bundle

ResetPasswordApiBundle is a simple way to generate API endpoints and send email to handle reset password logic of your users.
Can handle multiple User types/classes.

Install the package with: 

```console
composer require alpsify/reset-password-api-bundle
```

## Usage

Not supported by the MakerBundle yet ... That's too bad ... We are working on it :)

### Setting up manually

1- Create a PHP class named `ResetPasswordRequest` which extends `AbstractResetPasswordRequest` and implements the methods registerUser() and fetchUser(). Here an example with many types of users.

```php
// src/Entity/ResetPasswordRequest.php
use Alpsify\ResetPasswordAPIBundle\Model\AbstractResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass=ResetPasswordRequestRepository::class)
 */
class ResetPasswordRequest extends AbstractResetPasswordRequest
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Accountant::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $accountant;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return Accountant|object
     */
    public function getAccountant()
    {
        return $this->accountant;
    }

    /**
     * @param Accountant|object $accountant
     */
    public function setAccountant($accountant): void
    {
        $this->accountant = $accountant;
    }

    /**
     * @return Client|object
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client|object $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    public function registerUser(object $user): void
    {
        if($user instanceof User) {
            $this->user = $user;
        } elseif ($user instanceof Accountant) {
            $this->accountant = $user;
        } elseif ($user instanceof Client) {
            $this->client = $user;
        }
    }

    public function fetchUser(): object
    {
        if($this->user) {
            return $this->user;
        } elseif ($this->accountant) {
            return $this->accountant;
        } elseif ($this->client) {
            return $this->client;
        }
    }
}
```

2- Create the repository class and make it extend `ServiceEntityRepository` implements `ResetPasswordRequestRepositoryInterface`. Don't forget to also use the `ResetPasswordRequestRepositoryTrait` : if you want to override, feel free to do so.
```php
// App/Repository/ResetPasswordRequestRepository.php
use Alpsify\ResetPasswordAPIBundle\Persistence\Repository\ResetPasswordRequestRepositoryInterface;
use Alpsify\ResetPasswordAPIBundle\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use App\Entity\ResetPasswordRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ResetPasswordRequestRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }
}
```

3- Create the reset email template. Make it shiny and colorful :rainbow:
```twig
{# templates/email/reset_password.html.twig #}
{# Here you can do what ever you want to do. 

Send unicorns if you wish. :unicorn:

Most important you can access your token and the tokenLifetime in order 
to build something helpful for your user. Like a button to redirect to the frontend page for reset password ... 
#} 

<p>{{ token }}</p>
<p>{{ tokenLifetime }}</p>
```

## Configuration
Use the files your generate before to complete the configuration.

```yaml
#config/packages/alpsify_reset_password.yaml
alpsify_reset_password_api:
    token:

        # Life time of the request in seconds. After that the token is invalid and the user need to ask for a new one.
        lifetime:             3600

        # Customize the selector size of the token you send.
        selector_size:        20
        hash_algo:            ~

    # Time between 2 requests.
    throttle_time:        3600

    # Describe all your user types more
    # name: 
    #   class: 
    user_types:           # Required

        # Prototype
        user:
            class: App\Entity\User           # Required
        ...

    persistence:

        # Class of the entity used for storing the user reset password request.
        class:                ~

        # Repository class linked to the entity
        repository:           ~
    mailer:

        # Your choosen email. The reset email will be send through this one.
        from_email:           ~

        # Your choosen name link to the email.
        from_name:            ~

        # The template used by the mailer in order the send the reset link.
        template:             ~
```

### Parameters

//TODO

### Access new API endpoints
Don't forget to authorized everyone to access those endpoints by adding them in access_controle :

```yaml
#config/packages/security.yaml

...

    access_control:
      - { path: ^/api/request-reset-password, roles: PUBLIC_ACCESS, methods: [POST] }
      - { path: ^/api/reset-password, roles: PUBLIC_ACCESS, methods: [POST] }
      ...
```

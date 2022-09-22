<?php

namespace PhpUniter\PackageLaravel\Controller\Console\Cli;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpUniter\PackageLaravel\Application\PhpUnitUserRegisterService;
use Throwable;

class RegisterPhpUniterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'php-uniter:register {email} {password}';
    protected $name = 'php-uniter:register';

    /**
     * The console command description.
     */
    protected $description = 'Register PhpUniter user';

    /**
     * Execute the console command.
     */
    public function handle(PhpUnitUserRegisterService $registerService): ?int
    {
        try {
            $email = $this->argument('email');
            $password = $this->argument('password');

            $validator = Validator::make(
                ['email'    => $email, 'password' => $password],
                [
                    'email'    => 'required|string|email|max:255',
                    'password' => ['required', 'string'],
                ]);

            if (!is_string($email) || !is_string($password)) {
                throw new ValidationException($validator);
            }

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($registerService->process($email, $password)) {
                $this->info('User registered. Access token in your email. Put it in .env file - PHP_UNITER_ACCESS_TOKEN');
            }
        } catch (ValidationException $e) {
            $this->error("Command Validation Error: \n".$this->listMessages($e->errors()));

            return 1;
        } catch (GuzzleException $e) {
            $this->error($e->getMessage());

            return 1;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * @param string[] $messages
     */
    public function listMessages(array $messages): string
    {
        $res = '';
        foreach ($messages as $key=>$item) {
            $res .= $key.' => '.implode(' ', array_values($item))."\n";
        }

        return $res;
    }
}

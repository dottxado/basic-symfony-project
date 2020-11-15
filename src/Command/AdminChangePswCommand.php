<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Symfony\Component\String\u;

class AdminChangePswCommand extends Command
{
    protected static $defaultName = 'app:admin-change-psw';
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Change password for an administrative user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password of the user');
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('password') && null !== $input->getArgument('email')) {
            return;
        }

        $this->io->title('Change Admin Password Command Interactive Wizard');
        $this->io->text(
            [
                'If you prefer to not use this interactive wizard, provide the',
                'arguments required by this command as follows:',
                '',
                ' $ php bin/console app:admin-change-psw email@example.com password',
                '',
                'Now we\'ll ask you for the value of all the missing command arguments.',
            ]
        );

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null);
            $input->setArgument('email', $email);
        }
        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if (null !== $password) {
            $this->io->text(' > <info>New Password</info>: '.u('*')->repeat(u($password)->length()));
        } else {
            $password = $this->io->askHidden(
                'Password'
            );
            $input->setArgument('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        if (6 > strlen($plainPassword)) {
            throw new RuntimeException('The password must be longer than 6 characters');
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => $email]);

        if (is_null($existingUser) || !in_array('ROLE_ADMIN', $existingUser->getRoles())) {
            throw new RuntimeException(sprintf('There is no administrative user registered with the "%s" email.', $email));
        }

        $existingUser->setPassword($this->encoder->encodePassword($existingUser, $plainPassword));
        $this->entityManager->persist($existingUser);
        $this->entityManager->flush();

        $this->io->success(sprintf('Administrator user was successfully updated: %s', $email));

        return 0;
    }
}

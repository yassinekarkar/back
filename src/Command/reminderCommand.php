<?php

namespace App\Command;

use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MsJwtBundle\Manager\MailManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class reminderCommand extends Command
{

    use LockableTrait;

    private $entityManager;
    private $mailManager;
    private $translator;
    private $limit;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailManager $mailManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->mailManager = $mailManager;
        $this->limit = 200;
    }

    protected function configure()
    {
        $this->setName('Bill:send:reminder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->log('info', 'start send mail');
        $this->notifyClient();
        $this->log('info', 'end send mail');
    }

    protected function notifyClient()
    {
        $this->log('info', 'start send remind expired facture to Clients');
        $mailStacks = $this->entityManager
            ->getRepository(Facture::class)
            ->ReminderClient($this->limit);

        $sended = [];

        $this->log('info', 'sending ' . count($mailStacks) . ' mail');

        foreach ($mailStacks as $mailStack) {

            try {

                $this->log('info', 'sending to ' . $mailStack['mail_to']);

                $this->mailManager->sendMail(
                    $mailStack['mail'],
                    'facture_reminder',
                    $mailStack,
                    'Mail/reminder.html.twig'

                );
                $sended[] = $mailStack['id'];
            } catch (\Exception $ex) {
                $this->log('error', 'error sending to ' . $mailStack['mail_to'] . ' :' . $ex->getMessage());
            }
        }

    }
}
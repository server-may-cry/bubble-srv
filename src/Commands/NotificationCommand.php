<?php

namespace Commands;

use Saxulum\Console\Command\AbstractPimpleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use social\VK;

class NotificationCommand extends AbstractPimpleCommand
{
    protected function configure()
    {
        $this
            ->setName('notification:all')
            ->addArgument(
                'message',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $map = [
            0 => 'Скорее возвращайтесь в игру, шаропузик соскучился!',
            1 => 'В игре обновились ВСЕ уровни, ваш ежедневный бонус ждет вас в игре!',
        ];

        $msg = $input->getArgument('message');
        if(!isset($map[$msg])) {
            die('Unknown index '.$msg);
        }

        $msg = $map[$msg];

        $users = \R::findCollection('users', 'sys_id = ?', [1]);
        $ids = [];
        $rst = [];
        while($user = $users->next()) {
            $ids[] = $user->extId;
            if(count($ids) == 200) {
                $r = VK::sendNotification($ids, $msg);
                var_dump($r);
                $p = json_decode($r, true);
                if (isset($p['response'])) {
                    $sp = explode(',', $p['response']);
                    $rst = array_merge($rst, $sp);
                }
                $ids = [];
                sleep(2);
            }
        }
        $r = VK::sendNotification($ids, $msg);
        var_dump($r);
        $p = json_decode($r, true);
        if (isset($p['response'])) {
            $sp = explode(',', $p['response']);
            $rst = array_merge($rst, $sp);
        }
        $ids = [];
        sleep(2);
        var_dump(count($rst));
    }
}

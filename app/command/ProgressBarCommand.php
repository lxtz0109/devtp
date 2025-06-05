<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ProgressBarCommand extends Command
{
    protected function configure()
    {
        // 配置命令名称和描述
        $this->setName('progress:bar')
            ->setDescription('Show a progress bar');
    }

    protected function handle(Input $input, Output $output)
    {

        // 模拟要处理的数据总量
        $total = 100;

        // 创建进度条
        $progressBar = new ProgressBar( new ConsoleOutput(), $total);
        $progressBar->setFormat('verbose');

        $output->writeln('开始处理数据...');


        echo "开始时间：".date("Y-m-d H:i:s")."\n";
        // 模拟处理过程
        for ($i = 0; $i < $total; $i++) {
            sleep(60);
            // 更新进度条
            $progressBar->advance();

        }
        echo "结束时间：".date("Y-m-d H:i:s")."\n";


        // 完成进度条
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('数据处理完成!');

        




    }
}
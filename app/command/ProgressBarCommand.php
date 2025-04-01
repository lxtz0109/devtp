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

        // 模拟处理过程
        for ($i = 0; $i < $total; $i++) {
            sleep(2);
            // 更新进度条
            $progressBar->advance();

        }



        // 完成进度条
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('数据处理完成!');



        /*$total = 50;
        $barLength = 50;

        $output->writeln('开始处理:');

        for ($i = 1; $i <= $total; $i++) {
            // 计算进度百分比
            $percent = round(($i / $total) * 100);

            // 计算当前进度条长度
            $progress = round(($i / $total) * $barLength);

            // 构建进度条字符串
            $bar = str_repeat('=', $progress) . '>' . str_repeat(' ', $barLength - $progress);

            // 输出进度信息
            $output->write("\r[$bar] $percent%");

            // 模拟处理耗时
            usleep(100000); // 0.1秒
        }

        $output->writeln("\n处理完成!");*/
    }
}
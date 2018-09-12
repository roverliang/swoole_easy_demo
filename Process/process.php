<?php
/**
 * Desc: Swoole php 多进程demo
 * User: 梁子     
 * Time: 2018年9月12日 22:02
 */
class ProcessMange
{
    private $_processNum = 2;   //启动的进程数
    private $_masterPid;        //master进程 可以称之为父进程
    private $_childProssArr = [];

    /**
     *
     * 进程初始化
     */
     public function __construct(int $processNum=2)
     {
         $this->_processNum = $processNum;
         $this->_masterPid = posix_getpid();   //获取当前的进程ID
         $this->startProcess();                //启动子进程
         $this->processWait();                 //守护子进程
     }

    /**
     * 启动进程
     * Created by 梁子     
     * 2018/9/12
     */
     public function startProcess()
     {
         for($i=0; $i<$this->_processNum; $i++) {
             $process = new swoole_process(array($this, 'onStartProcess'), false, false);
             $pid = $process->start();
             $this->_childProssArr[$pid] = 1;
         }
         return true;
     }

    /**
     * 当启动子进程的时候调用
     * Created by 梁子     
     * 2018/9/12
     * @param $worker
     */
     public function onStartProcess($worker)
     {
         for($i=0; $i<10;$i++) {
             echo "进程{$worker->pid}第{$i}次运行".PHP_EOL;
             sleep(1);
             echo PHP_EOL;
         }
         return true;
     }


    /**
     * 守护子进程，避免产生僵死进程
     * Created by 梁子     
     * 2018/9/12
     */
     public function processWait()
     {
         while(1) {
             if (count($this->_childProssArr)) {
                 $ret = swoole_process::wait();
                 if (is_array($ret)) {
                     echo "进程:{$ret['pid']}退出".PHP_EOL;
                     unset($this->_childProssArr[$ret['pid']]);
                 }
             } else {
                 echo "所有进程全部退出".PHP_EOL;
                 break;
             }
         }
     }
}


new ProcessMange();



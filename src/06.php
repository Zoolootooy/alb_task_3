<?php
echo 'Start' . PHP_EOL;


$childPids = [];
$Arr = [[]];
$start = [];

$N = 20;
$M = 10000;

for ($i = 0; $i < $N; $i++){
    for ($j = 0; $j < $M; $j++){
        $Arr[$i][$j] = rand(0,1000);
    }
}



for ($i = 0; $i < $N; $i++) {

    $newPid = pcntl_fork();

    if ($newPid == -1) {
        die ('Can\'t fork process');
    } elseif ($newPid) {
        $childPids[] = $newPid;
        echo 'Main process have created subprocess  ' . $newPid . PHP_EOL;
        $start[$i] = microtime(true);
        //main func

        if ($i == $N) {
            echo 'Main process is waiting for all subprocesses  ' . PHP_EOL;
            foreach ($childPids as $childPid) {
                pcntl_waitpid($childPid, $status);
                echo 'OK. Subprocess ' . $childPid . ' is ready' . PHP_EOL;


//                $sharedId = shmop_open($childPid, 'a', 0, 0);
//                $shareData = shmop_read($sharedId, 0, shmop_size($sharedId));
//
//
//                shmop_delete($sharedId);
//                shmop_close($sharedId);
            }
            echo 'OK. All subprocesses are ready' . PHP_EOL;
        }

    } else {
        $myPid = getmypid();
        echo 'I am forked process with pid ' . $myPid . PHP_EOL;
        $timeout = rand(1000000, 2000000);
        usleep($timeout);
        main($i);
        echo 'I am already done ' . $myPid . PHP_EOL;


//        $shareData = serialize($timeout);
//        $sharedId = shmop_open($myPid, 'c', 0644, strlen($shareData));
//        shmop_write($sharedId, $shareData, 0);

        die(0);
    }
}

function main($i)
{
    $array = [[]];
    $array = $GLOBALS['Arr'];
//    print_r($array);
    $res = $array[$i];
    for ($j = 1; $j < $GLOBALS['M']; $j++){
        $F = 0;
        for ($k = 0; $k < ($GLOBALS['M']-$j); $k++){
            if ($res[$k] > $res[$k+1]){
                $tmp = $res[$k];
                $res[$k] = $res[$k+1];
                $res[$k+1] = $tmp;
                $F=1;

            }
        }
        if ($F == 0){
            break;
        }
    }
    $array[$i] = $res;
    $GLOBALS['Arr'] = $array;
    return $res;
}
echo 'Main body' . PHP_EOL;
echo 'Main stop' . PHP_EOL;





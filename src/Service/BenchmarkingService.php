<?php

namespace App\Service;


class BenchmarkingService {

    /**'
     * @var NotificationService
     */
    private $notificationService;

    /**
     * BenchmarkingService constructor.
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handles a process of benchmarking service
     * @param string $mainUrl
     * @param array $otherUrls
     * @return string
     */
    public function handleProcess(string $mainUrl,array $otherUrls): string
    {
        $contents = [];
        $errors= [];
        $contents[] = ['Url', 'Execution time', 'Difference'];
        try{
            $mainUrlTime = $this->getTime($mainUrl);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
        $contents[] = [$mainUrl, $mainUrlTime, '-'];
        foreach ($otherUrls as $key => $url){
            try {
                $urlTime = $this->getTime($url);
            }catch (\Exception $exception){
                return $exception->getMessage();
            }
            $difference = $urlTime - $mainUrlTime;
            if($difference < 0){
                try{
                    $this->notificationService->sendEmail();
                }catch (\Exception $exception){
                    $errors[] = [$exception->getMessage()];
                }
            }elseif ($difference < 0 && $difference <= $mainUrlTime/2){
                try{
                    $this->notificationService->sendSms();
                }catch (\Exception $exception){
                    $errors[] = [$exception->getMessage()];
                }
            }
            $contents[] = [$url, $urlTime, $difference];
        }
        $contents[] = ['Date of the test', date("Y-m-d H:i:s")];

        $this->logIntoFile($contents);
        $result = $this->transformIntoText(array_merge($contents, $errors));

        return $result;
    }

    /**
     * @param string $url
     * @return float
     * @throws \Exception
     */
    private function getTime(string $url) :float
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        if(curl_exec($ch) === false)
        {
            throw new \Exception('Remember that all arguments has to be urls in a proper form.');
        }

        curl_close($ch);

        return $totalTime;
    }

    /**
     * Logs the information into text file
     * @param array $content
     */
    private function logIntoFile(array $content): void
    {
        $filename = 'log.txt';
        $text = $this->transformIntoText($content);
        file_put_contents($filename, $text, FILE_APPEND | LOCK_EX);
    }

    /**
     * Transforms array to readable text
     * @param array $content
     * @return string
     */
    private function transformIntoText(array $content): string
    {
        $str = array_reduce($content, function ($str, $elem){
            return $str .= implode(' | ', $elem) . PHP_EOL;
        });
        return $str;
    }

}
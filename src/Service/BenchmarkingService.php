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
     * @param $mainUrl
     * @param $otherUrls
     * @return string
     */
    public function handleProcess($mainUrl, $otherUrls)
    {
        $contents[] = ['Url', 'Execution time', 'Difference'];
        $mainUrlTime = $this->getTime($mainUrl);
        $contents[] = [$mainUrl, $mainUrlTime, '-'];
        $errors= [];
        foreach ($otherUrls as $key => $url){
            $urlTime = $this->getTime($url);
            $difference = $urlTime - $mainUrlTime;
            if($difference < 0){
                try{
                    $this->notificationService->sendEmail();
                }catch (\Exception $exception){
                    $errors[] = $exception->getMessage();
                }
            }elseif ($difference < 0 && $difference <= $mainUrlTime/2){
                try{
                    $this->notificationService->sendSms();
                }catch (\Exception $exception){
                    $errors[] = $exception->getMessage();
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
     * @param $url
     * @return float
     */
    private function getTime($url) :float
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        if(curl_exec($ch) === false)
        {
            return 'There was and error. Curl error:' . curl_error($ch);
        }

        curl_close($ch);

        return $totalTime;
    }

    /**
     * Logs the information into text file
     * @param $content
     */
    private function logIntoFile($content): void
    {
        touch('log.txt');
        $fp = fopen('log.txt', 'w');
        fwrite($fp, $this->transformIntoText($content));
        fclose($fp);
    }

    /**
     * Transforms array to readable text
     * @param $content
     * @return string
     */
    private function transformIntoText($content): string
    {
        $str = "";
        foreach ($content as $row){
            $str .= implode(' | ', $row) . PHP_EOL;
        }
        return $str;
    }

}
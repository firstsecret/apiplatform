<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/30
 * Time: 10:55
 */

namespace App\Admin\Extensions\Tools;


use App\Exceptions\GzReadException;
use Encore\Admin\LogViewer\LogViewer;

class NginxLogViewer extends LogViewer
{
    public function parseLog($raw)
    {
        // TODO: Implement parseLog() method.
        return $raw;
//        $logs = preg_split('/\[(\d{4}(?:-\d{2}){2} \d{2}(?::\d{2}){2})\] (\w+)\.(\w+):((?:(?!{"exception").)*)?/', trim($raw), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
//
//        foreach ($logs as $index => $log) {
//            if (preg_match('/^\d{4}/', $log)) {
//                break;
//            } else {
//                unset($logs[$index]);
//            }
//        }
//
//        if (empty($logs)) {
//            return [];
//        }
//
//        $parsed = [];
//
//        foreach (array_chunk($logs, 5) as $log) {
//            $parsed[] = [
//                'time' => $log[0] ?? '',
//                'env' => $log[1] ?? '',
//                'level' => $log[2] ?? '',
//                'info' => $log[3] ?? '',
//                'trace' => trim($log[4] ?? ''),
//            ];
//        }
//
//        unset($logs);
//
//        rsort($parsed);
//
//        return $parsed;
    }

    /**
     * Get previous page url.
     *
     * @return bool|string
     */
    public function getPrevPageUrl()
    {
        if ($this->pageOffset['end'] >= $this->getFilesize() - 1) {
            return false;
        }

        return route('custom-nginx-log', [
            'file' => $this->file, 'offset' => $this->pageOffset['end'],
        ]);
    }

    /**
     * Get Next page url.
     *
     * @return bool|string
     */
    public function getNextPageUrl()
    {
        if ($this->pageOffset['start'] == 0) {
            return false;
        }

        return route('custom-nginx-log', [
            'file' => $this->file, 'offset' => -$this->pageOffset['start'],
        ]);
    }

    public function getLogFiles($count = 20)
    {
        $files = glob('/data/wwwlogs/*');

        $files = array_combine($files, array_map('filemtime', $files));
        arsort($files);

        $files = array_map('basename', array_keys($files));

        return array_slice($files, 0, $count);
    }

    public function getFilePath()
    {
        if (!$this->filePath) {

            $path = sprintf('/data/wwwlogs/%s', $this->file);

            if (!file_exists($path)) {
                throw new \Exception('log not exists!');
            }

            $this->filePath = $path;
        }

        return $this->filePath;
    }

    public function gzFetch($seek = 0, $lines = 20, $buffer = 4096)
    {
        $f = gzopen($this->filePath, 'rb');
        if ($seek) {
            gzseek($f, abs($seek));
        } else {
            gzseek($f, 0);
        }

        if ($seek >= 0) {
            $output = '';

            $this->pageOffset['start'] = gztell($f);

            while (!gzeof($f) && $lines >= 0) {
                $chunk = gzread($f, $buffer);
//                if (!$chunk) {
//                    continue;
//                }
                $output = $output . $chunk;
                $lines -= substr_count($chunk, "\n");
            }

            $this->pageOffset['end'] = gztell($f);

            while ($lines++ < 0) {
                $strpos = strrpos($output, "\n") + 1;
                $_ = mb_strlen($output, '8bit') - $strpos;
                $output = substr($output, 0, $strpos);
                $this->pageOffset['end'] -= $_;
            }
        } else {
            // gz 不支持
            // 从后往前读

            $output = '';

            $this->pageOffset['end'] = gztell($f);

            while (gztell($f) > 0 && $lines >= 0) {
                $offset = min(gztell($f), $buffer);
                gzseek($f, -$offset, SEEK_CUR);
                $output = ($chunk = gzread($f, $offset)) . $output;
                gzseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                $lines -= substr_count($chunk, "\n");
            }

            $this->pageOffset['start'] = gztell($f);

            while ($lines++ < 0) {
                $strpos = strpos($output, "\n") + 1;
                $output = substr($output, $strpos);
                $this->pageOffset['start'] += $strpos;
            }
        }

        gzclose($f);

        return $this->parseLog($output);
    }

    public function fetch($seek = 0, $lines = 20, $buffer = 4096)
    {
        // if is gz
        $info = pathinfo($this->filePath);
        $this->filext = $info['extension'];
        if ($info['extension'] == 'gz') {
            // uzip
            return $this->gzFetch($seek, $lines, $buffer);
        }

        $f = fopen($this->filePath, 'rb');

        if ($seek) {
            fseek($f, abs($seek));
        } else {
            fseek($f, 0, SEEK_END);
        }

        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }
        fseek($f, -1, SEEK_CUR);

        // 从前往后读,上一页
        // Start reading
        if ($seek > 0) {
            $output = '';

            $this->pageOffset['start'] = ftell($f);

            while (!feof($f) && $lines >= 0) {
                $output = $output . ($chunk = fread($f, $buffer));
                $lines -= substr_count($chunk, "\n");
            }

            $this->pageOffset['end'] = ftell($f);

            while ($lines++ < 0) {
                $strpos = strrpos($output, "\n") + 1;
                $_ = mb_strlen($output, '8bit') - $strpos;
                $output = substr($output, 0, $strpos);
                $this->pageOffset['end'] -= $_;
            }

            // 从后往前读,下一页
        } else {
            $output = '';

            $this->pageOffset['end'] = ftell($f);

            while (ftell($f) > 0 && $lines >= 0) {
                $offset = min(ftell($f), $buffer);
                fseek($f, -$offset, SEEK_CUR);
                $output = ($chunk = fread($f, $offset)) . $output;
                fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                $lines -= substr_count($chunk, "\n");
            }

            $this->pageOffset['start'] = ftell($f);

            while ($lines++ < 0) {
                $strpos = strpos($output, "\n") + 1;
                $output = substr($output, $strpos);
                $this->pageOffset['start'] += $strpos;
            }
        }

        fclose($f);

        return $this->parseLog($output);
    }
}
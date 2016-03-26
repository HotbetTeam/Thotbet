<?php

class Time_Fn extends _function {
    
    public function live($timestamp, $style=false){
        $timestamp = strtotime($timestamp);
        $difference = time() - $timestamp;
        $periods = array("วินาที", "นาที", "ชั่วโมง");
        $ending = "ที่แล้ว";

        $dayName = array(0 => "วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสษบดี", "วันศุกร์", "วันเสาร์");
        $strDate = date("j", $timestamp) . " " . $this->month( date("n", $timestamp), $style);
        $strYear = date("Y", $timestamp) + 543;
        $strTimes = " เวลา " . date("H:i", $timestamp) . " น.";
        $dataDate = $this->day(date('w', $timestamp), true) . "ที่ $strDate $strYear $strTimes";

        if ($difference < 60) {
            $j = 0;
            $periods[$j].=($difference != 1) ? "" : "";
            $text = "$difference $periods[$j]$ending";
            $text = ($text == "0 วินาทีที่แล้ว") ? "ไม่กี่$periods[$j]$ending" : "$difference $periods[$j]$ending";
        } elseif ($difference < 3600) { // นาที
            $j = 1;
            $difference = round($difference / 60);
            $periods[$j].=($difference != 1) ? "" : "";
            $text = "$difference $periods[$j]$ending";
        } elseif ($difference < 86400) { // ชม
            $j = 2;
            $difference = round($difference / 3600);
            $periods[$j].=($difference != 1) ? "" : "";
            $difference = ($difference != 1) ? $difference : "ประมาณ 1";
            $text = "$difference $periods[$j]$ending";
        } elseif ($difference < 172800) {
            $difference = round($difference / 86400);
            $text = "เมื่อวานนี้ " . " เวลา " . date("H.i", $timestamp) . " น.";
        } elseif ($difference < 259200) {
            $difference = round($difference / 172800);
            $text = "เมื่อ" . $this->day(date("w", $timestamp), true) . " เวลา " . date("H.i", $timestamp) . " น.";
        } else {
            $text = $strDate;

            if ($timestamp < strtotime(date("Y-01-01 00:00:00")))
                $text .= " " . $strYear;
            $text .= $strTimes;
        }

        $text = '<abbr title="' . $dataDate . '" data-utime="' . $timestamp . '" class="timestamp livetimestamp">' . $text . '</abbr>';
        
        if($style == true){
            $text = '<abbr title="' . $dataDate . '" data-utime="' . $timestamp . '" class="timestamp livetimestamp">' . $dataDate . '</abbr>';
        }
        return $text;
    }

    public function stamp($timestamp=null){
        $timestamp = strtotime($timestamp);
        $difference = time() - $timestamp;
        $periods = array("วิ", "น.", "ชม.");

        $today = date('Y/m/d');
        $text = "";

        if ($difference < 60) { // วินาที
            $j = 0;
            $text = "$difference $periods[$j]";
        } elseif ($difference < 3600) { // นาที
            $j = 1;
            $difference = round($difference / 60);
            $text = "$difference $periods[$j]";
        } elseif ($difference < 86400) { // ชม
            $j = 2;
            $difference = round($difference / 3600);
            $text = "$difference $periods[$j]";
        }else{

            $deDate = date("j", $timestamp)." ".$this->month(date("n", $timestamp), false);
            $deDate .=( date("Y", strtotime($today))!=date('Y',$timestamp) )? " ".substr( (date('Y',$timestamp)+543), 2, 2): "";

            switch ($difference) {
                case '-86400':
                    $text = 'เมื่อวาน';
                    break;
                case 0:
                    $text = 'วันนี้';
                    break;
                case 86400:
                    $text = 'พรุ่งนี้';
                    break;
                
                default:
                    $text = $deDate;
                    break;
            }
        }

        $theDate = date("j", $timestamp);
        $theMonth = $this->month(date("n", $timestamp), true);
        $theYear = date("Y", $timestamp) + 543;
        $theTime = date("H.s", $timestamp);

        $title = "$theDate $theMonth $theYear เวลา {$theTime}น.";

        $text = '<span class="timestamp" data-time="'.$timestamp.'" data-plugins="tooltip" title="'.$title.'" >'.$text.'</span>';
        return $text;
    }

    public function day($length,$style=false){
        if($style===false)
        $arr = array(0 => "อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส.");

        else
        $arr = array(0 => "วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสษบดี", "วันศุกร์", "วันเสาร์");
        
        return $arr[$length];
    }

    public function month($length,$style=false){
        if($style===false)
        $arr = array(1 => "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
        
        else
        $arr = array(1 => "มกราคม", "กุมภาาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        
        return $arr[$length];
    }

    public function full($timestamp,$short=false, $showyear=false) { 
        
        $theDate = date("j", $timestamp);
        $theMonth = $this->month(date("n", $timestamp), $short);
        $theYear = date("Y", $timestamp)!=date("Y") || $showyear? date("Y", $timestamp) + 543: "";
        $theTime = date("H.s", $timestamp);

        return "$theDate $theMonth $theYear เวลา {$theTime}น.";
    }

    public function normal($timestamp, $short=false) {
        $year = date('Y', $timestamp)+543;

        if( !$short ){
            $year = substr($year, 2);
        }
        return date('j', $timestamp)." ". $this->month( date('n', $timestamp), $short )." ". $year;
    }
}
?>
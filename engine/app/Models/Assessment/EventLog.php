<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'eventId';

    protected $table = 'event_logs';

    public static function saveEvent($username=null,$eventCategory=null,$eventLevel=null,$fullName=null,$eventStatus=null,$action=null,
                                     $description=null,$ipAddress=null,$macAddress=null,$controllerName=null,$functionName=null,$exception=null,
                                     $trace=null,$lineNo=null,$customMessage=null){
        $event = new EventLog();
        $event->username = $username;
        $event->eventCategory = $eventCategory;
        $event->eventLevel = $eventLevel;
        $event->fullName = $fullName;
        $event->eventStatus = $eventStatus;
        $event->action = $action;
        $event->description = $description;
        $event->ipAddress = $ipAddress;
        $event->macAddress = $macAddress;
        $event->controllerName = $controllerName;
        $event->functionName = $functionName;
        $event->exception = $exception;
        $event->trace = $trace;
        $event->lineNo = $lineNo;
        $event->customMessage = $customMessage;
        $event->save();

        return $event;
    }

    /**
     * get ip address
     */
    public static function getIpAddress(){
        $ipAddress = '';
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            // to get shared ISP IP address
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check for IPs passing through proxy servers
            // check if multiple IP addresses are set and take the first one
            $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipAddressList as $ip) {
                if (! empty($ip)) {
                    // if you prefer, you can check for valid IP address here
                    $ipAddress = $ip;
                    break;
                }
            }
        } else if (! empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } else if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (! empty($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if (! empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipAddress;
    }

    /**
     * get mac address
     */
    public static function getMacAddress(){
        ob_start();
        system('ipconfig /all');
        $mycom=ob_get_contents();
        ob_clean();
        $findme = 'Physical Address';
        $pmac = strpos($mycom, $findme);
        $mac=substr($mycom,($pmac+33),17);
        return $mac;
    }
}

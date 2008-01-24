<?php
/**
 * Peoplefinder HTML Renderer
 * 
 * PHP version 5
 * 
 * @package   UNL_Peoplefinder
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2007 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://peoplefinder.unl.edu/
 */

/**
 * Determines if a network in the form of 192.168.17.1/16 or
 * 127.0.0.1/255.255.255.255 or 10.0.0.1 matches a given ip
 * @param $network The network and mask
 * @param $ip The ip to check
 * @return bool true or false
 */
function net_match($network, $ip) {
     $ip_arr = explode('/', $network);
     $network_long = ip2long($ip_arr[0]);
     $x = ip2long($ip_arr[1]);
     $mask =  long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
     $ip_long = ip2long($ip);
     return ($ip_long & $mask) == ($network_long & $mask);
}

class UNL_Peoplefinder_Renderer_HTML
{
    
    protected $trustedIP = false;
    public $uri;
    
    public $displayLimit;
    
    /** This can be set to a javascript function name to send the UID to when clicking a uid */
    public $uid_onclick;
    /** This defines a mode in which the directory is searched to return one user. */
    public $choose_uid = false;
    public $page_onclick;
    
    function __construct(array $options = null)
    {
        $validIPs = array('129.93.0.0/16','65.123.32.0/19','64.39.240.0/20','216.128.208.0/20');
        foreach ($validIPs as $range) {
            if (net_match($range, $_SERVER['REMOTE_ADDR'])) {
                $this->trustedIP = true;
                break;
            }
        }
        $this->displayLimit = UNL_PF_DISPLAY_LIMIT;
        $this->uri          = $_SERVER['SCRIPT_NAME'];
        if (isset($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * This function sets parameters for this class.
     * 
     * @param array $options an associative array of options to set.
     */
    function setOptions(array $options)
    {
        foreach ($options as $option=>$val) {
            if (property_exists($this,$option)) {
                $this->$option = $val;
            } else {
                echo 'Warning: Trying to set unkown option ['.$option.'] for object '.get_class($this)."\n";
            }
        }
    }

    public function renderRecord(UNL_Peoplefinder_Record $r)
    {
        echo "<div class='vcard'>\n";
        if(isset($r->mail) && ($r->eduPersonPrimaryAffiliation != 'student' || $this->displayStudentEmail==true)) {
            $displayEmail = true;
        } else {
            $displayEmail = false;
        }
        if ($displayEmail && isset($r->unlEmailAlias)) echo "<a class='email' href='mailto:{$r->unlEmailAlias}@unl.edu'>";
        echo '<span class="fn">'.$r->displayName.'</span>'.PHP_EOL;
        if (isset($r->eduPersonNickname)) echo '<span class="nickname">'.$r->eduPersonNickname.'</span>'.PHP_EOL;
        if ($displayEmail && isset($r->unlEmailAlias)) echo "</a>\n";
        echo '<span class="eppa">('.$r->eduPersonPrimaryAffiliation.')</span>'.PHP_EOL;
        echo '<div class="vcardInfo">'.PHP_EOL;
        if (isset($r->unlSISClassLevel)) {
            switch ($r->unlSISClassLevel) {
                case 'FR':
                    $class = 'Freshman,';
                    break;
                case 'SR':
                    $class = 'Senior,';
                    break;
                case 'SO':
                    $class = 'Sophomore,';
                    break;
                case 'JR':
                    $class = 'Junior,';
                    break;
                case 'GR':
                    $class = 'Graduate Student,';
                    break;
                default:
                    $class = $r->unlSISClassLevel;
            }
            echo '<span class="title">'.$class." ".$r->unlSISMajor."&ndash;".$r->unlSISCollege.'</span>';
        }
        if (isset($r->unlSISLocalAddr1) || isset($r->unlSISPermAddr1)) {
            $localaddr = array($r->unlSISLocalAddr1, $r->unlSISLocalAddr2, $r->unlSISLocalCity, $r->unlSISLocalState, $r->unlSISLocalZip);
            $permaddr  = array($r->unlSISPermAddr1, $r->unlSISPermAddr2, $r->unlSISPermCity, $r->unlSISPermState, $r->unlSISPermZip);
            $this->renderAddress($localaddr, 'Local', 'workAdr');
            $this->renderAddress($permaddr, 'Home', 'homeAdr');
        }
        echo "<span class='title'>{$r->title}</span>\n";
        echo "<span class='org'>\n\t<span class='organization-name'>University of Nebraska-Lincoln</span>\n\t<span class='organization-unit'>{$r->unlHRPrimaryDepartment}</span>\n</span>\n";
        if (isset($r->postalAddress)) {
            if (strpos($r->postalAddress,'UNL')!= -1 || strpos($r->postalAddress,'UNO')!= -1) {
                $address = $r->formatpostalAddress();
                echo '<div id="workAdr" class="adr">
                     <span class="type">Work</span>
                     <span class="street-address">'. $address['street-address'] . '</span>
                     <span class="locality">' . $address['locality'] . '</span> 
                     <span class="region">' . $address['region'] . '</span>
                     <span class="postal-code">' . $address['postal-code'] . '</span>
                     <div class="country-name">USA</div>
                    </div>'.PHP_EOL;
            } else {
                echo "<span class='adr'>{$r->postalAddress}</span>\n";
            }
        }
        if ($r->eduPersonPrimaryAffiliation != 'student') {
            echo '<div id="workTel" class="tel">
                     <span class="type">Work</span>
                     <span class="value"><a href="tel:'.$r->telephoneNumber.'">'.$r->telephoneNumber.'</a></span>
                    </div>'.PHP_EOL;
        }
        if ($displayEmail) {
            echo "<span class='email'><a class='email' href='mailto:{$r->unlEmailAlias}@unl.edu'>{$r->unlEmailAlias}@unl.edu</a></span>\n";
            if ($this->trustedIP===true) echo "<span class='email'>Delivery Address: {$r->mail}</span>\n";
        }
        $linktext = '<img src="http://www.unl.edu/unlpub/2004sharedgraphics/icon_vcard.gif" alt="V-Card" />'.PHP_EOL;
        echo $this->getVCardLink($r->uid, $linktext, null, 'Download V-Card for '.$r->givenName.' '.$r->sn);
        echo '</div>'.PHP_EOL.'</div>'.PHP_EOL;
    }
    
    public function renderAddress($address, $type, $id = null)
    {
        if (isset($id)) {
            $id = 'id="'.$id.'" ';
        } else {
            $id = '';
        }
        $addr = '
        <div '.$id.'class="adr">
         <span class="type">'.$type.'</span>
         <span class="street-address">'.$address[0].'</span>
         <span class="locality">'.$address[2].'</span>
         <span class="region">'.$address[3].'</span>
         <span class="postal-code">'.$address[4].'</span>';
        if (isset($address[5])) {
            $addr .= '<div class="country-name">'.$address[5].'</div>'; 
        }
        $addr .= '</div>';
        echo $addr;
    }
    
    public function formatAddress($addressArray)
    {
        /********************************************************
        *    This function takes in an array of address information and formats it
        *    $addressArray[0] = Address line 1
        *    $addressArray[1] = Address line 2
        *    $addressArray[2] = City
        *    $addressArray[3] = State
        *    $addressArray[4] = Zip
        *    $addressArray[5] = Country
        *********************************************************/
        if (isset($addressArray[0])) {
            $address = $addressArray[0]."<br />";
            if (isset($addressArray[1])) $address .= $addressArray[1]."<br />";
            $address .= $addressArray[2].", ".$addressArray[3]." ".$addressArray[4];
            if (isset($addressArray[5])) $address .= "<br />".$addressArray[4];
        }
        else $address = 'Unlisted';
        return $address;
    }
    
    public function displayPageLinks($num_records, $start, $end)
    {
        //Display Page information
        $page = (isset($_GET['p']))?$_GET['p']:0;
        $next = $page + 1;
        if ($page>=1) $prevLink = '<a class="previous" href="'.$this->uri.'?'.preg_replace('/[&]?p=\d/','',$_SERVER['QUERY_STRING']).'&amp;p='.($page-1).'">&lt;&lt;&nbsp;</a>';
        else $prevLink = '&lt;&lt;&nbsp;';
        if ($end < $num_records) $nextLink = "<a class='next' href='".$this->uri."?".preg_replace("/[&]?p=\d/","",$_SERVER['QUERY_STRING'])."&amp;p=$next'>&nbsp;&gt;&gt;</a>";
        else $nextLink = '&nbsp;&gt;&gt;';
        return '<div class="cNav">'.$prevLink.$nextLink.'</div>';
    }
    
    public function renderListRecord(UNL_Peoplefinder_Record $r)
    {
        $linktext = $r->sn . ',&nbsp;'. $r->givenName;
        if (isset($r->eduPersonNickname)) {
            $linktext .= ' "'.$r->eduPersonNickname.'"';
        }
        echo '<div class="fn">'.$this->getUIDLink($r->uid, $linktext, $this->uid_onclick).'</div>'.PHP_EOL;
        if (isset($r->eduPersonPrimaryAffiliation)) echo '<div class="eppa">('.$r->eduPersonPrimaryAffiliation.')</div>'.PHP_EOL;
        if (isset($r->unlHRPrimaryDepartment)) echo '<div class="organization-unit">'.$r->unlHRPrimaryDepartment.'</div>'.PHP_EOL;
        if (isset($r->telephoneNumber)) echo '<div class="tel"><a href="tel:'.$r->telephoneNumber.'">'.$r->telephoneNumber.'</a></div>'.PHP_EOL;
        
        echo $this->getUIDLink($r->uid, 'contact info', $this->uid_onclick, 'cInfo');
		if ($this->choose_uid) {
		    echo '<div class="pfchooser"><a href="#" onclick="return pfCatchUID(\''.$r->uid.'\');">Choose this person</a></div>'.PHP_EOL;
		}
    }
    
    public function renderSearchResults(array $records, $start=0, $num_rows=UNL_PF_DISPLAY_LIMIT)
    {
        if (($start+$num_rows)>count($records)) {
            $end = count($records);
        } else {
            $end = $start+$num_rows;
        }
        if ($start > 0 || $end < count($records)) {
            $navlinks = $this->displayPageLinks(count($records), $start, $end);
        } else {
            $navlinks = '';
        }
        echo "<div class='result_head'>Results ".($start+1)." - $end out of ".count($records).':'.$navlinks.'</div>'.PHP_EOL;
        echo '<ul>';
        for ($i = $start; $i<$end; $i++) {
            $even_odd = ($i % 2) ? '' : 'alt';
            echo '<li class="ppl_Sresult '.$even_odd.'">';
            $this->renderListRecord($records[$i]);
            echo '</li>'.PHP_EOL;
        }
        echo '</ul>';
        echo "<div class='result_head'>$navlinks</div>";
    }
    
    public function getUIDLink($uid, $linktext = null, $onclick = null, $class = null)
    {
        $uri = $this->uri.'?uid='.$uid;
        if (isset($linktext)) {
            $link = '<a href="'.$uri.'"';
            if (isset($onclick)) {
                $link .= ' onclick="return '.$this->uid_onclick.'(\''.$uid.'\');"';
            }
            if (isset($class)) {
                $link .= ' class="'.$class.'"';
            }
            $link .= '>'.$linktext.'</a>';
            return $link;
        } else {
            return $uri;
        }
    }
    
    public function getVCardLink($uid, $linktext = null,$onclick = null,$title = null)
    {
        $uri = 'http://peoplefinder.unl.edu/vcards/'.$uid;
        if (isset($linktext)) {
            $link = '<a href="'.$uri.'"';
            if (isset($onclick)) {
                $link .= ' onclick="return '.$onclick.'(\''.$uid.'\');"';
            }
            if (isset($title)) {
                $link .= ' title="'.$title.'"';
            }
            $link .= '>'.$linktext.'</a>';
            return $link;
        } else {
            return $uri;
        }
    }
}

?>
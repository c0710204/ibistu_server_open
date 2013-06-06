<?php
class helper
{	static $list;
	static function makechangelist()
	{
		self::$list['<![CDATA[']='';
		self::$list["\n\t\t\n                  \n\n                 \n\n\t"]='';
		self::$list["\n\t\n         \n         \n\t"]='';
		self::$list[']]>']='';
		self::$list["\n"]='<br>';
		self::$list["><br>"]=">\n";
		self::$list[">   <br>"]=">\n";
		self::$list["\t<br>"]="\t";
		//self::$list[" <br>"]=" ";
		self::$list["</d>\n<br>"]="</d>";
		self::$list["<br>"]='|br|';
	}
	static function makechange_xml($str)
	{
		self::makechangelist();
		$str=html_entity_decode($str,ENT_NOQUOTES ,'UTF-8');
//		$str=str_replace('<![CDATA[', '', $str);
//		$str=str_replace(']]>', '', $str);
//		$str=str_replace('&ldquo', '"', $str);
//		$str=str_replace('&rdquo', '"', $str);
		foreach (self::$list as $key=>$value)
		{
			$str=str_replace($key, $value, $str);
		}
		return $str;
	}
	function sql_getTableLength($sql,$data)
	{
		$q=$sql->query('select count(*) as len from '.mysql_real_escape_string($data['table_length']));
		$ans=$sql->fetch_assoc($q);
		return $ans['len'];
	}
	static $monthinyear00=array(0,31,59,90,120,151,181,212,243,273,304,334,365,396,424,455,485,516,546,577,608,638,669,699,730);
	static $monthinyear40=array(0,31,60,91,121,152,182,213,244,274,305,335,366,397,425,456,486,517,547,578,609,639,670,700,731);
	static  $monthinyear04=array(0,31,59,90,120,151,181,212,243,273,304,334,365,396,425,456,486,517,547,578,609,639,670,700,731);
	static function helper_makeWeekList($start,$end,$kind)
	{
	//	echo "$start|$end|$kind<br/>";
		if ($kind=='')
		{
			$bet=1;
		}
		else
		{
			$bet=2;
			if ($kind=='单')
			{
				$start=(floor($start/2)*2)+1;
				$end=(floor($end/2)*2)-1;
			}
			else 
			{
				$start=(floor($start/2)*2)+2;
				$end=(floor($end/2)*2);				
			}
		}
		$ans=array();
		for($i=$start;$i<=$end;$i+=$bet)
		{
			array_push($ans,$i);
		}
		return $ans;
	}
	static function helper_makeDayList($weekarray,$weekday,$startday=0)
	{
		if ($startday==0)
		{
			$startday=$cfg['termStart'];
		}
		$ans=array();
		foreach ($weekarray as $val)
		{
			$d=($val-1)*7+$weekday-1;
			$day=$this->helper_date_add_day($startday, $d);
			array_push($ans,$day);
		}
		return $ans;
	}
	static function helper_date_add_day($date,$day)
	{
		$monthinyear='monthinyear00';
		$ydm=explode('-', $date);
		if ($ydm[0]%4==0)
		{	
			$monthinyear='monthinyear40';
		}
		elseif (($ydm[0]+1)%4==0)
		{
			$monthinyear='monthinyear04';
		}
		$ydm[2]+=$day;
		$temp=$this->$monthinyear;
		$ydm[2]+=$temp[$ydm[1]-1];
		
		for($i=1;$i<=26;$i++)
		{
			if ($ydm[2]<$temp[$i])
			{
				break;
			}
		}
	//	ECHO $ydm[0].'-'.$ydm[1].'-'.$ydm[2].'|'.$i.'<br>';
		$ydm[1]=($i)%12;
		$ydm[0]+=floor($i/12);
		$ydm[2]=$ydm[2]-$temp[$i-1];
	//	ECHO $ydm[2].'|'.($i-1).'<br>';
		if ($ydm[1]<10) $ydm[1]='0'.$ydm[1];
		if ($ydm[2]<10) $ydm[2]='0'.$ydm[2];
		return $ydm[0].'-'.$ydm[1].'-'.$ydm[2];
		
	}
}

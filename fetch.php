<?php
	/*
		Author: learnrpg@gmail.com
		相本最後測試可執行正確日期：2015/2/15
	*/
	$link = "";
	
	function GetBetween($content,$start,$end)
	{
		$r = explode($start, $content);
		if (isset($r[1]))
		{
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return '';
	}

	function GetRoot($link)
	{
		$start = strpos($link , 'http');
		$end = strpos($link, '/album');
		
		return substr($link, $start, $end-$start);
	}

	$download_count = 0;
	function Download($match)
	{
		global $download_count;
		
		foreach($match as $item)
		{
			$item = str_replace("_s", "_b", $item);
			
			$start = strpos($item , 'http');
			$end = strpos($item, '"', $start);
			
			$link_address = substr($item, $start, $end-$start);
			//echo $link_address;
			
			$filename_start = strrpos($link_address, '/');
			$filename = substr($link_address, $filename_start+1, $end);
			//echo $filename;
			
			if (file_put_contents($filename, file_get_contents(substr($item, $start, $end-$start))) != FALSE)		
				$download_count++;
		}
	}

	// Main function
	$root = GetRoot($link);
	
	//分析此相本所有頁面
	$download_count = 0;
	$count = 0;
	do
	{
		$count++;
		//取得指定位址的內容，並儲存至text
		$text = file_get_contents($link);
		//$text = mb_convert_encoding($text, "UTF-8", "BIG5" /*"EUC-JP"*/);
		
		preg_match_all('/<img class="thumb" (.*)<\/a>/sU', $text, $match);
		//印出match
		//print_r($match);
		//print_r( strip_tags($match[1][0]));

		echo $link."<br />";
		foreach($match[0] as $item)
		{
			//$item = mb_convert_encoding($item, "UTF-8", "big5" /*"EUC-JP"*/);
			
			echo $item."\r\n";	
		}
		echo "<br />";
		
		Download($match[0]);
		
		$start = strpos($text, 'a class="nextBtn"');
		preg_match_all('/<a class="nextBtn" href="(.*)">下一頁<\/a>/sU', $text, $nextpage);
		//print_r($nextpage);
		if ($start != NULL)
			$link = $root.$nextpage[1][0];	
		
	} while ($start != NULL && $count < 10);

	echo "共下載了 ".$download_count." 張圖形";
?>
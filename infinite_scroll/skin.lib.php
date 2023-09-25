<?
$arr_search = array();
for($j = 1; $j < 11; $j++){
	if(isset($_GET["wr_".$j]) && @strlen($_GET["wr_".$j]) > 0 && !is_array($_GET["wr_".$j])){
		$qstr .= "&wr_".$j."=".urlencode($_GET["wr_".$j]);
	}else if(isset($_GET["wr_".$j])  && @is_array($_GET["wr_".$j])){
		for($x = 0; $x < count($_GET["wr_".$j]);$x++){
			$qstr .= "&wr_".$j."[]=".@urlencode($_GET["wr_".$j][$x]);
		}
	}
}

// 검색 구문을 얻는다.
function get_sql_search_all($search_ca_name, $search_field, $search_text, $search_operator='and',$search_arr='')
{
    global $g5;

    $str = "";
    if ($search_ca_name)
        $str = " ca_name = '$search_ca_name' ";

    $search_text = strip_tags(($search_text));
    $search_text = trim(stripslashes($search_text));

    if (!$search_text && $search_text !== '0' && !$search_arr) {
        if ($search_ca_name) {
            return $str;
        } else {
            return '0';
        }
    }

		if(count($search_arr['name']) > 0){
			$se_flag = 0;
			for($j = 0; $j < count($search_arr['name']); $j++){
				if(is_array($search_arr['val'][$j])){
					$str2 = "";

					for($x = 0; $x < count($search_arr['val'][$j]);$x++){
						$str2 = append_sql2($str2, " or ", " {$search_arr['name'][$j]} like '%{$search_arr['val'][$j][$x]}%' ");
					}
					$str2 = " (".$str2.") ";
					$str = append_sql2($str, " and", $str2);

				}else if(strlen($search_arr['val'][$j]) > 0 && $search_arr['val'][$j] ){
							$str = append_sql2($str, " and ", " {$search_arr['name'][$j]}  like '%{$search_arr['val'][$j]}%' ");
						
					
				}
			}
		}

		if(strlen(@trim($search_text))){

			if ($str)
        $str .= " and ";

    // 쿼리의 속도를 높이기 위하여 ( ) 는 최소화 한다.
    $op1 = "";

    // 검색어를 구분자로 나눈다. 여기서는 공백
    $s = array();
    $s = explode(" ", $search_text);

    // 검색필드를 구분자로 나눈다. 여기서는 +
    $tmp = array();
    $tmp = explode(",", trim($search_field));
    $field = explode("||", $tmp[0]);
    $not_comment = "";
    if (!empty($tmp[1]))
        $not_comment = $tmp[1];

    $str .= "(";
    for ($i=0; $i<count($s); $i++) {
        // 검색어
        $search_str = trim($s[$i]);
        if ($search_str == "") continue;

        // 인기검색어
        //insert_popular($field, $search_str);

        $str .= $op1;
        $str .= "(";

        $op2 = "";
        for ($k=0; $k<count($field); $k++) { // 필드의 수만큼 다중 필드 검색 가능 (필드1+필드2...)

            // SQL Injection 방지
            // 필드값에 a-z A-Z 0-9 _ , | 이외의 값이 있다면 검색필드를 wr_subject 로 설정한다.
            $field[$k] = preg_match("/^[\w\,\|]+$/", $field[$k]) ? strtolower($field[$k]) : "wr_subject";

            $str .= $op2;
            switch ($field[$k]) {
                case "mb_id" :
                case "wr_name" :
                    $str .= " $field[$k] = '$s[$i]' ";
                    break;
                case "wr_hit" :
                case "wr_good" :
                case "wr_nogood" :
                    $str .= " $field[$k] >= '$s[$i]' ";
                    break;
                // 번호는 해당 검색어에 -1 을 곱함
                case "wr_num" :
                    $str .= "$field[$k] = ".((-1)*$s[$i]);
                    break;
                case "wr_ip" :
                case "wr_password" :
                    $str .= "1=0"; // 항상 거짓
                    break;
                // LIKE 보다 INSTR 속도가 빠름
                default :
                    if (preg_match("/[a-zA-Z]/", $search_str))
                        $str .= "INSTR(LOWER($field[$k]), LOWER('$search_str'))";
                    else
                        $str .= "INSTR($field[$k], '$search_str')";
                    break;
            }
            $op2 = " or ";
        }
        $str .= ")";

        $op1 = " $search_operator ";
    }
    $str .= " ) ";
		}
    if (@$not_comment)
        $str .= " and wr_is_comment = '0' ";

    return $str;
}

// where 조건문자열 추가
function append_sql2($sql, $appender, $text) {
	if(strlen($sql) > 0) {
		$sql = $sql.$appender.$text;
	} else {
		$sql = $text;
	}
	return $sql;
}


$arr_search = array();

for($j = 1; $j < 11; $j++){
	if((@isset($_GET["wr_".$j]) && @strlen($_GET["wr_".$j]) > 0 ) || @is_array($_GET["wr_".$j])) {
		$arr_search['name'][] = "wr_".$j;
		$arr_search['val'][] = $_GET["wr_".$j];
	}
}

if(@strlen($_GET[wr_id]) > 0){			// View
	
	$sql_search = "";
	// 검색이면
	if ($sca || $stx || $stx === '0' || count($arr_search) > 0) {

			$se_arr = array();
			$se_arr = $arr_search;

			// where 문을 얻음
			$sql_search = get_sql_search_all($sca, $sfl, $stx, $sop, $se_arr);
			$search_href = get_pretty_url($bo_table,'','&amp;page='.$page.$qstr);

			$list_href = get_pretty_url($bo_table);

	} else {
			$search_href = '';
			$list_href = get_pretty_url($bo_table,'',$qstr);
	}

	if (!$board['bo_use_list_view']) {
			if ($sql_search)
					$sql_search = " and " . $sql_search;

			// 윗글을 얻음
			$sql = " select wr_id, wr_subject, wr_datetime from {$write_table} where wr_is_comment = 0 and wr_num = '{$write['wr_num']}' and wr_reply < '{$write['wr_reply']}' {$sql_search} order by wr_num desc, wr_reply desc limit 1 ";
			$prev = sql_fetch($sql);
			// 위의 쿼리문으로 값을 얻지 못했다면
			if (!$prev['wr_id'])     {
					$sql = " select wr_id, wr_subject, wr_datetime from {$write_table} where wr_is_comment = 0 and wr_num < '{$write['wr_num']}' {$sql_search} order by wr_num desc, wr_reply desc limit 1 ";
					$prev = sql_fetch($sql);
			}

			// 아래글을 얻음
			$sql = " select wr_id, wr_subject, wr_datetime from {$write_table} where wr_is_comment = 0 and wr_num = '{$write['wr_num']}' and wr_reply > '{$write['wr_reply']}' {$sql_search} order by wr_num, wr_reply limit 1 ";
			$next = sql_fetch($sql);
			// 위의 쿼리문으로 값을 얻지 못했다면
			if (!$next['wr_id']) {
					$sql = " select wr_id, wr_subject, wr_datetime from {$write_table} where wr_is_comment = 0 and wr_num > '{$write['wr_num']}' {$sql_search} order by wr_num, wr_reply limit 1 ";
					$next = sql_fetch($sql);
			}
	}

	// 이전글 링크
	$prev_href = '';
	if (isset($prev['wr_id']) && $prev['wr_id']) {
			$prev_wr_subject = get_text(cut_str($prev['wr_subject'], 255));
			$prev_href = get_pretty_url($bo_table, $prev['wr_id'], $qstr);
			$prev_wr_date = $prev['wr_datetime'];
	}

	// 다음글 링크
	$next_href = '';
	if (isset($next['wr_id']) && $next['wr_id']) {
			$next_wr_subject = get_text(cut_str($next['wr_subject'], 255));
			$next_href = get_pretty_url($bo_table, $next['wr_id'], $qstr);
			$next_wr_date = $next['wr_datetime'];
	}

	// 쓰기 링크
	$write_href = '';
	if ($member['mb_level'] >= $board['bo_write_level']) {
			$write_href = short_url_clean(G5_BBS_URL.'/write.php?bo_table='.$bo_table);
	}

	// 답변 링크
	$reply_href = '';
	if ($member['mb_level'] >= $board['bo_reply_level']) {
			$reply_href = short_url_clean(G5_BBS_URL.'/write.php?w=r&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
	}

	// 수정, 삭제 링크
	$update_href = $delete_href = '';
	// 로그인중이고 자신의 글이라면 또는 관리자라면 비밀번호를 묻지 않고 바로 수정, 삭제 가능
	if (($member['mb_id'] && ($member['mb_id'] === $write['mb_id'])) || $is_admin) {
			$update_href = short_url_clean(G5_BBS_URL.'/write.php?w=u&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr);
			set_session('ss_delete_token', $token = uniqid(time()));
			$delete_href = G5_BBS_URL.'/delete.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;token='.$token.'&amp;page='.$page.urldecode($qstr);
	}
	else if (!$write['mb_id']) { // 회원이 쓴 글이 아니라면
			$update_href = G5_BBS_URL.'/password.php?w=u&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr;
			$delete_href = G5_BBS_URL.'/password.php?w=d&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr;
	}

	// 최고, 그룹관리자라면 글 복사, 이동 가능
	$copy_href = $move_href = '';
	if ($write['wr_reply'] == '' && ($is_admin == 'super' || $is_admin == 'group')) {
			$copy_href = G5_BBS_URL.'/move.php?sw=copy&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr;
			$move_href = G5_BBS_URL.'/move.php?sw=move&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;page='.$page.$qstr;
	}

	$scrap_href = '';
	$good_href = '';
	$nogood_href = '';
	if ($is_member) {
			// 스크랩 링크
			$scrap_href = G5_BBS_URL.'/scrap_popin.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;

			// 추천 링크
			if ($board['bo_use_good'])
					$good_href = G5_BBS_URL.'/good.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;good=good';

			// 비추천 링크
			if ($board['bo_use_nogood'])
					$nogood_href = G5_BBS_URL.'/good.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;good=nogood';
	}

}else{														// List
	$sop = strtolower($sop);
	if ($sop != 'and' && $sop != 'or')
			$sop = 'and';

	// 분류 선택 또는 검색어가 있다면
	$stx = trim($stx);
	//검색인지 아닌지 구분하는 변수 초기화
	$is_search_bbs = false;

	if ($sca || $stx || $stx === '0' || count($arr_search) > 0) {     //검색이면

			$se_arr = array();
			$se_arr = $arr_search;

			$is_search_bbs = true;      //검색구분변수 true 지정
			$sql_search = get_sql_search_all($sca, $sfl, $stx, $sop, $se_arr);

			// 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
			$sql = " select MIN(wr_num) as min_wr_num from {$write_table} ";
			$row = sql_fetch($sql);
			$min_spt = (int)$row['min_wr_num'];

			if (!$spt) $spt = $min_spt;

			$sql_search .= " and (wr_num between {$spt} and ({$spt} + {$config['cf_search_part']})) ";

			// 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
			// 라엘님 제안 코드로 대체 http://sir.kr/g5_bug/2922
			$sql = " SELECT COUNT(DISTINCT `wr_parent`) AS `cnt` FROM {$write_table} WHERE {$sql_search} ";
			$row = sql_fetch($sql);
			$total_count = $row['cnt'];
			/*
			$sql = " select distinct wr_parent from {$write_table} where {$sql_search} ";
			$result = sql_query($sql);
			$total_count = sql_num_rows($result);
			*/
	} else {
			$sql_search = "";

			$total_count = $board['bo_count_write'];
	}

	if(G5_IS_MOBILE) {
			$page_rows = $board['bo_mobile_page_rows'];
			$list_page_rows = $board['bo_mobile_page_rows'];
	} else {
			$page_rows = $board['bo_page_rows'];
			$list_page_rows = $board['bo_page_rows'];
	}

	if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

	// 년도 2자리
	$today2 = G5_TIME_YMD;

	$list = array();
	$i = 0;
	$notice_count = 0;
	$notice_array = array();

	// 공지 처리
	if (!$is_search_bbs) {
			$arr_notice = explode(',', trim($board['bo_notice']));
			$from_notice_idx = ($page - 1) * $page_rows;
			if($from_notice_idx < 0)
					$from_notice_idx = 0;
			$board_notice_count = count($arr_notice);

			for ($k=0; $k<$board_notice_count; $k++) {
					if (trim($arr_notice[$k]) == '') continue;

					$row = sql_fetch(" select * from {$write_table} where wr_id = '{$arr_notice[$k]}' ");

					if (!$row['wr_id']) continue;

					$notice_array[] = $row['wr_id'];

					if($k < $from_notice_idx) continue;

					$list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
					$list[$i]['is_notice'] = true;

					$i++;
					$notice_count++;

					if($notice_count >= $list_page_rows)
							break;
			}
	}

	$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
	$from_record = ($page - 1) * $page_rows; // 시작 열을 구함

	// 공지글이 있으면 변수에 반영
	if(!empty($notice_array)) {
			$from_record -= count($notice_array);

			if($from_record < 0)
					$from_record = 0;

			if($notice_count > 0)
					$page_rows -= $notice_count;

			if($page_rows < 0)
					$page_rows = $list_page_rows;
	}

	// 관리자라면 CheckBox 보임
	$is_checkbox = false;
	if ($is_member && ($is_admin == 'super' || $group['gr_admin'] == $member['mb_id'] || $board['bo_admin'] == $member['mb_id']))
			$is_checkbox = true;

	// 정렬에 사용하는 QUERY_STRING
	$qstr2 = 'bo_table='.$bo_table.'&amp;sop='.$sop;

	// 0 으로 나눌시 오류를 방지하기 위하여 값이 없으면 1 로 설정
	$bo_gallery_cols = $board['bo_gallery_cols'] ? $board['bo_gallery_cols'] : 1;
	$td_width = (int)(100 / $bo_gallery_cols);

	// 정렬
	// 인덱스 필드가 아니면 정렬에 사용하지 않음
	//if (!$sst || ($sst && !(strstr($sst, 'wr_id') || strstr($sst, "wr_datetime")))) {
	if (!$sst) {
			if ($board['bo_sort_field']) {
					$sst = $board['bo_sort_field'];
			} else {
					$sst  = "wr_num, wr_reply";
					$sod = "";
			}
	} else {
			$board_sort_fields = get_board_sort_fields($board, 1);
			if (!$sod && array_key_exists($sst, $board_sort_fields)) {
					$sst = $board_sort_fields[$sst];
			} else {
					// 게시물 리스트의 정렬 대상 필드가 아니라면 공백으로 (nasca 님 09.06.16)
					// 리스트에서 다른 필드로 정렬을 하려면 아래의 코드에 해당 필드를 추가하세요.
					// $sst = preg_match("/^(wr_subject|wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
					$sst = preg_match("/^(wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
			}
	}

	if(!$sst)
			$sst  = "wr_num, wr_reply";

	if ($sst) {
			$sql_order = " order by {$sst} {$sod} ";
	}

	if ($is_search_bbs) {
			$sql = " select distinct wr_parent from {$write_table} where {$sql_search} {$sql_order} limit {$from_record}, $page_rows ";
	} else {
			$sql = " select * from {$write_table} where wr_is_comment = 0 ";
			if(!empty($notice_array))
					$sql .= " and wr_id not in (".implode(', ', $notice_array).") ";
			$sql .= " {$sql_order} limit {$from_record}, $page_rows ";
	}
	
	// 페이지의 공지개수가 목록수 보다 작을 때만 실행
	if($page_rows > 0) {
			$result = sql_query($sql);

			$k = 0;

			while ($row = sql_fetch_array($result))
			{
					// 검색일 경우 wr_id만 얻었으므로 다시 한행을 얻는다
					if ($is_search_bbs)
							$row = sql_fetch(" select * from {$write_table} where wr_id = '{$row['wr_parent']}' ");

					$list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
					if (strstr($sfl, 'subject')) {
							$list[$i]['subject'] = search_font($stx, $list[$i]['subject']);
					}
					$list[$i]['is_notice'] = false;
					$list_num = $total_count - ($page - 1) * $list_page_rows - $notice_count;
					$list[$i]['num'] = $list_num - $k;

					$i++;
					$k++;
			}
	}

	g5_latest_cache_data($board['bo_table'], $list);

	$write_pages = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));

	$list_href = '';
	$prev_part_href = '';
	$next_part_href = '';
	if ($is_search_bbs) {
			$list_href = get_pretty_url($bo_table);

			$patterns = array('#&amp;page=[0-9]*#', '#&amp;spt=[0-9\-]*#');

			//if ($prev_spt >= $min_spt)
			$prev_spt = $spt - $config['cf_search_part'];
			if (isset($min_spt) && $prev_spt >= $min_spt) {
					$qstr1 = preg_replace($patterns, '', $qstr);
					$prev_part_href = get_pretty_url($bo_table,0,$qstr1.'&amp;spt='.$prev_spt.'&amp;page=1');
					$write_pages = page_insertbefore($write_pages, '<a href="'.$prev_part_href.'" class="pg_page pg_prev">이전검색</a>');
			}

			$next_spt = $spt + $config['cf_search_part'];
			if ($next_spt < 0) {
					$qstr1 = preg_replace($patterns, '', $qstr);
					$next_part_href = get_pretty_url($bo_table,0,$qstr1.'&amp;spt='.$next_spt.'&amp;page=1');
					$write_pages = page_insertafter($write_pages, '<a href="'.$next_part_href.'" class="pg_page pg_end">다음검색</a>');
			}
	}


	$write_href = '';
	if ($member['mb_level'] >= $board['bo_write_level']) {
			$write_href = short_url_clean(G5_BBS_URL.'/write.php?bo_table='.$bo_table);
	}

	$nobr_begin = $nobr_end = "";
	if (preg_match("/gecko|firefox/i", $_SERVER['HTTP_USER_AGENT'])) {
			$nobr_begin = '<nobr>';
			$nobr_end   = '</nobr>';
	}

	// RSS 보기 사용에 체크가 되어 있어야 RSS 보기 가능 061106
	$rss_href = '';
	if ($board['bo_use_rss_view']) {
			$rss_href = G5_BBS_URL.'/rss.php?bo_table='.$bo_table;
	}

	$stx = get_text(stripslashes($stx));
}



?> 
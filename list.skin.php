<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);


function get_vimeoThumb($id) {

  $apiurl = "http://vimeo.com/api/v2/video/".$id.".json";

  $curlsession = curl_init (); 

  curl_setopt ($curlsession, CURLOPT_URL, $apiurl); 

  curl_setopt ($curlsession, CURLOPT_POST, 0); 

  curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER, 1);

  $response = curl_exec ($curlsession); 



  $JSONreturns = json_decode($response);

  return  $JSONreturns[0]->thumbnail_large;

}
// 비메오 썸네일 추출함수
?>



<div id="bo_gall" style="width:<?php echo $width; ?>">
    
    <div class="portfolio_serach_wrap clearfix">
      <div class="portfolio_search_left">
        키워드
      </div>
      <div class="portfolio_search_middle">
        <div class='hsx-search-wrap'>
          <div>
            <form name="fsearch" method="get" style="">
              <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
              <input type="hidden" name="sca" value="<?php echo $sca ?>">
              <input type="hidden" name="sop" value="and">
              <input type="hidden" name="sfl" value="wr_subject||wr_content||ca_name">
              <div class='hsx-search-box clearfix'>
                <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" id="stx" class="hsx-search-ipt" maxlength="20" placeholder="찾고 싶은 키워드를 검색해 보세요." >
                <button type="submit" class="hsx-search-submit">
                  <img src="<?php echo G5_THEME_URL ?>/img/search-icon.png" alt="">
                  <!-- Search -->
                </button>
              </div>
            </form>
          </div>
      </div>
      </div>
      <div class="portfolio_search_right">
        <ul class="search_right_box clearfix">
          <li class="serach_right_tit">
            자주 찾는 키워드
          </li>
          <li class="search_right_keyword">
            <a href="/portfolio/bbs/board.php?bo_table=infinite_scroll"><span>전체보기</span></a>
            <a href="/portfolio/bbs/board.php?bo_table=infinite_scroll&sca=&sop=and&sfl=wr_subject%7C%7Cwr_content%7C%7Cca_name&stx&wr_1=이미지"><span>이미지</span></a>
            <a href="/portfolio/bbs/board.php?bo_table=infinite_scroll&sca=&sop=and&sfl=wr_subject%7C%7Cwr_content%7C%7Cca_name&stx&wr_1=유튜브"><span>유튜브</span></a>
            <a href="/portfolio/bbs/board.php?bo_table=infinite_scroll&sca=&sop=and&sfl=wr_subject%7C%7Cwr_content%7C%7Cca_name&stx&wr_1=비메오"><span>비메오</span></a>
          </li>
        </ul>
      </div>
    </div>

<!-- 게시판 목록 시작 { -->
<div id="bo_gall" style="width:<?php echo $width; ?>">

    <?php if ($is_category) { ?>
    <!-- <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> 카테고리</h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav> -->
    <?php } ?>

    <form name="fboardlist"  id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- 게시판 페이지 정보 및 버튼 시작 { -->
    <div id="bo_btn_top">
        <!-- <div id="bo_list_total">
            <span>Total <?php echo number_format($total_count) ?>건</span>
            <?php echo $page ?> 페이지
        </div> -->

        <?php if ($rss_href || $write_href) { ?>
        <ul class="btn_bo_user">
        	<?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn" title="관리자"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a></li><?php } ?>
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn" title="RSS"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <li>
            	<button type="button" class="btn_bo_sch btn_b01 btn" title="게시판 검색"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">게시판 검색</span></button> 
            </li>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01 btn" title="글쓰기"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sound_only">글쓰기</span></a></li><?php } ?>
        	<?php if ($is_admin == 'super' || $is_auth) {  ?>
        	<li>
        		<button type="button" class="btn_more_opt is_list_btn btn_b01 btn" title="게시판 리스트 옵션"><i class="fa fa-ellipsis-v" aria-hidden="true"></i><span class="sound_only">게시판 리스트 옵션</span></button>
        		<?php if ($is_checkbox) { ?>	
		        <ul class="more_opt is_list_btn">  
		            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"><i class="fa fa-trash-o" aria-hidden="true"></i> 선택삭제</button></li>
		            <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"><i class="fa fa-files-o" aria-hidden="true"></i> 선택복사</button></li>
		            <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"><i class="fa fa-arrows" aria-hidden="true"></i> 선택이동</button></li>
		        </ul>
		        <?php } ?>
        	</li>
        	<?php }  ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } 게시판 페이지 정보 및 버튼 끝 -->

    <?php if ($is_checkbox) { ?>
    <div id="gall_allchk" class="all_chk chk_box">
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" class="selec_chk">
    	<label for="chkall">
        	<span></span>
        	<b class="sound_only">현재 페이지 게시물 </b> 전체선택
        </label>
    </div>
    <?php } ?>

    <div class="moreTop">
            <div class="loading">
                <span></span>   <!--1. span은 하나의 원이다. -->
                <span></span>
                <span></span>
            </div>
        </div>

    <ul id="gall_ul" class="gall_row">
      <article>

        <?php for ($i=0; $i<count($list); $i++) {

            $classes = array();
            
            $classes[] = 'gall_li';
         ?>
        <li class="<?php echo implode(' ', $classes); ?> gall_li_<?php echo $i ?> gall_li <?php echo $list[$i]['wr_1']?>"<?php if($list[$i]['wr_1'] == 'PopupVideo' || $list[$i]['wr_1'] == 'PopupImage') { ?> id='<?php echo $list[$i]['wr_id'] ?><?php } ?>'>
        
        


          <?php if($is_admin){ ?>
            <a href="<?php echo $list[$i]['href'] ?>" class='admin_viewBtn'>
          <?php } else if($list[$i]['wr_1'] == 'PopupVideo' || $list[$i]['wr_1'] == 'PopupImage' || $list[$i]['wr_1'] == 'PopupVimeo'){ ?>
            <a href="<?php echo $list[$i]['href'] ?>" onclick="gallWrId(this, event);" class='client_popupBtn'>
          <?php } else{?>
            <a href="<?php echo $list[$i]['wr_2'] ?>" target="_blank">
          <?php } ?>
            <div class="gall_box">
                <div class="gall_chk chk_box">
	                <?php if ($is_checkbox) { ?>
					        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="selec_chk">
	                <label for="chk_wr_id_<?php echo $i ?>">
	                	<span></span>
	                	<b class="sound_only"><?php echo $list[$i]['subject'] ?></b>
	                </label>
	                
	                <?php } ?>
	                <span class="sound_only">
	                    <?php
	                    if ($wr_id == $list[$i]['wr_id'])
	                        echo "<span class=\"bo_current\">열람중</span>";
	                    else
	                        echo $list[$i]['num'];
	                     ?>
	                </span>
                </div>
                <div class="gall_con">
                    <div class="gall_img">
                      <div>
                          <?php
                          // $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);

                          // $file = get_file($bo_table, $list[$i]['wr_id']);
                          // if(preg_match("/\.({$config['cf_image_extension']})$/i", $file[0]['file'])) {
                          //     $file_src = '<img src="'.$file[0]['path'].'/'.$file[0]['file'].'" width="" height="">';
                          // } else {
                          //     $file_src = '';
                          // }

                          $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], 0, false, false);

                          if($thumb['src']) {
                            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
                          } else if($list[$i]['wr_10']){
                            $img_content = '<img src="https://img.youtube.com/vi/'.$list[$i]['wr_10'].'/mqdefault.jpg">';
                          } else if($list[$i]['wr_8']){
                            $img_url = $list[$i]['wr_8'];
                            $img_content = "<img src='$img_url'>";
                          } else if($list[$i]['wr_9']){
                            $videoId = $list[$i]['wr_9'];
                            $thumb_Url = get_vimeoThumb($videoId);
                            $img_content = "<img src='$thumb_Url'>";
                          }else {
                            $img_content = '<span class="no_image">no image</span>';
                          }

                         
          

                            if($img_content) {
                              // echo $file_src;
                              echo $img_content;

                            } 
                            // else if ($list[$i]['wr_10']){
                            //   $img_content = '<img src="https://s.ytimg.com/yts/img/favicon_144-vfliLAfaB.png" style="position:absolute; width:30px; margin:15px 20px;"><img src="https://img.youtube.com/vi/'.$list[$i]['wr_10'].'/mqdefault.jpg">';
                            //   echo $img_content;

                            // } else {
                            //   $img_content = '<div class="no_image">No Image</div>';
                            //   echo $img_content;
                            // }
                          ?>
                      </div>
                    </div>
                </div>
            </div>
            </a>
        </li>
        <?php } ?>
        <?php if (count($list) == 0) { echo "<li class=\"empty_list\">게시물이 없습니다.</li>"; } ?>
      </article>

    </ul>

    <div class="moreBtm">
            <div class="loading">
                <span></span>   <!--1. span은 하나의 원이다. -->
                <span></span>
                <span></span>
            </div>
        </div>
	
	<!-- 페이지 -->
	<?php echo $write_pages; ?>
	<!-- 페이지 -->
	
	<?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="bo_fx">
        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
        	<?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn" title="관리자"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a></li><?php } ?>
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn" title="RSS"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01 btn" title="글쓰기"><i class="fa fa-pencil" aria-hidden="true"></i><span class="sound_only">글쓰기</span></a></li><?php } ?>
        </ul>	
        <?php } ?>
    </div>
    <?php } ?> 
    </form>

    <!-- 게시판 검색 시작 { -->
    <div class="bo_sch_wrap">	
        <fieldset class="bo_sch">
            <h3>검색</h3>
            <form name="fsearch" method="get">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca" value="<?php echo $sca ?>">
            <input type="hidden" name="sop" value="and">
            <label for="sfl" class="sound_only">검색대상</label>
            <select name="sfl" id="sfl">
                <?php echo get_board_sfl_select_options($sfl); ?>
            </select>
            <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
            <div class="sch_bar">
                <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="검색어를 입력해주세요">
                <button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
            </div>
            <button type="button" class="bo_sch_cls"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
            </form>
        </fieldset>
        <div class="bo_sch_bg"></div>
    </div>
    <script>
        // 게시판 검색
        $(".btn_bo_sch").on("click", function() {
            $(".bo_sch_wrap").toggle();
        })
        $('.bo_sch_bg, .bo_sch_cls').click(function(){
            $('.bo_sch_wrap').hide();
        });
    </script>
    <!-- } 게시판 검색 끝 -->
</div>


<div class="portpolio_popup">
  <div class='portfolio_popup_contents'>
    <div class="portfolio_popup_closeBtn">×</div>
    <main></main>
  </div>
  <div class="portfolio_popup_bg"></div>
  <div class="portfolio_popup_loading"></div>
</div>


<script>
  $(".cate_left").click(function(){
    $(this).siblings(".cate_right").stop().slideToggle();
    $(this).children('img').stop().toggleClass("on");
  })



  function cateNaviHasActive(){
    $('.cate_right_li').each(function(){
      if($(this).hasClass("active")){
        $(this).parents(".cate_right").siblings('.cate_left').addClass("thisNavi");
        $(this).parents(".cate_right").slideDown(0);
      }
    });

    $(".cate_left").each(function(){
      if($(this).hasClass("thisNavi")){
        $(this).children('img').addClass("on");
      };
    });
  }


  function cateNaviActive(){
    var url = window.location.href;
    $('.cate_right_li > a').each(function () {
      if (url.indexOf($(this).attr('href')) > -1) {
        $(this).parent(".cate_right_li").addClass('active');
        cateNaviHasActive();
        // moveScrollLeft2();
        // moveScrollLeft3();
      }
    });
  }
  cateNaviActive();
  $(".cate_right_li > a").mouseenter(function(){
    $(".cate_right_li").removeClass("active");
  });

  $(".cate_right_li > a").mouseleave(function(){
    cateNaviActive();
  });
</script>

<script>
    $('body').addClass("full");
    setTimeout(() => $('body').removeClass("full"), 600);

    function resizeGridItem(item){
      grid = document.querySelector("#gall_ul article");[0];
      // grid = document.getElementsByClassName("grid")[0];
      rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));
      rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'));
      rowSpan = Math.ceil((item.querySelector('.gall_li a').getBoundingClientRect().height+rowGap)/(rowHeight+rowGap));
        item.style.gridRowEnd = "span "+rowSpan;
      }

      function resizeAllGridItems(){
      allItems = document.getElementsByClassName("gall_li");
      for(x=0;x<allItems.length;x++){
        resizeGridItem(allItems[x]);
      }
      }

      // function resizeInstance(instance){
      //   item = instance.elements[0];
      //   resizeGridItem(item);
      // }
      function gallAni(){
        setTimeout(function(){
          $(".gall_li").each(function(){
            $(this).addClass("on");
          });
        });
      };
      window.onload = resizeAllGridItems();
      window.onload = setTimeout(() => resizeAllGridItems(), 600);
      window.addEventListener("resize", resizeAllGridItems);
      gallAni();

        // allItems = document.getElementsByClassName("item");
        // for(x=0;x<allItems.length;x++){
        // imagesLoaded( allItems[x], resizeInstance);
        // }

	var total_page = "<?=$total_page?>";
	var now_page = "<?=$page?>";
	var roll_page = now_page;

	
	$(window).ready(function(){
		console.log(now_page);
        console.log(total_page);
        if(total_page == 1){
            $("#footer").removeClass("scrollPage");
        } else {
            $("#footer").addClass("scrollPage");
        }
		if(now_page != 1){
			$(".topScroll").show();
		}

		if(roll_page != total_page){
			$(".btmScroll").show();
		}
	});

	$(window).scroll(function(){
        // console.log(roll_page);
        // console.log(total_page);
		var chkBtm = parseInt($(document).height()) - parseInt($(window).height());
    var ParScrolltop = parseInt($(window).scrollTop());
    var ParScrolltopPlus = ParScrolltop+1;

    // console.log(chkBtm);
    // console.log(ParScrolltop);
		if(chkBtm == parseInt($(window).scrollTop()) || $(window).scrollTop() + $(window).height() == $(document).height()){
    // if($(window).scrollTop() + $(window).height() == $(document).height()){
			roll_page++;
			
			if(roll_page <= total_page){
				callContent(roll_page,'append');
			}
		}else if($(window).scrollTop() == 0){
			
			now_page--;
			if(now_page > 0){
				callContent(now_page,'prepend');
			}
			
		}
    resizeAllGridItems();
	});



	function callContent(a,b){

		if(b=='append'){
			$(".moreBtm").slideDown();
		}else{
			$(".moreTop").slideDown();
		}
		var url = "<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>&page="+a+"&sca=<?=$sca?>";
		var tbody = "";
		var thtml = "";
		$.ajax({
			type:"POST",
			url:url,
			dataType : "html",
			success: function(html){
				tbody = html.split('<article>');
				thtml = tbody[1].split('</article>');

				setTimeout(function() { 
					if(b=='append'){
						$("article").append(thtml[0]);
					}else{
						$("article").prepend(thtml[0]);
					}
					$(".moreBar").slideUp();
					
					if(now_page == 1){
						$(".topScroll").slideUp();
					}

					if(roll_page >= total_page){
						$(".btmScroll").slideUp();
            $("#footer").removeClass("scrollPage");
            $(".moreBtm").slideUp();
					}
          // gallWrId();
				}, 400);
			},

      complete: function () {
        // setTimeout(function(){
        //   resizeAllGridItems();
        // }, 700);
        resizeAllGridItems();

        setTimeout(function(){
          $(".moreBtm, .moreTop").slideUp();
          resizeAllGridItems();
          gallAni();
        }, 900);

      },
			error: function(xhr, status, error) {
				alert(error);
			}  
		});
	}



	function popupView(wr_id, link){

  var url = link;
  var tbody = "";
  var thtml = "";
  $.ajax({
    type:"POST",
    url:url,
    dataType : "html",
    success: function(html){
      tbody = html.split('<main>');
      thtml = tbody[1].split('</main>');

      setTimeout(function() { 
        $("main").append(thtml[0]);
      }, 500);
    },
    beforeSend: function (){
      $(".portfolio_popup_loading").show();
    },
    complete: function () {
      setTimeout(function() { 
        $(".portfolio_popup_loading").hide();
        $(".portfolio_popup_closeBtn").show();
      }, 500);
    },
    error: function(xhr, status, error) {
      alert(error);
    }  
  });
  }

  // function gallWrId(){
  //   $(".PopupVideo, .PopupImage").each(function(){
  //     var wr_id = "#"+$(this).attr("id");
  //     var link = $(this).children(".client_popupBtn").attr("href");
  //     $(this).click(function (){ 
  //       if($(".portpolio_popup").hasClass("done")){
  //         $(".portpolio_popup").removeClass("done");
  //         return false
  //       } else {
  //         $(".portpolio_popup").show(0);
  //         $(".portpolio_popup").addClass("done");
  //         popupView(wr_id, link);
  //         setTimeout(function() { 
  //           $(".portfolio_popup_bg, .portfolio_popup_closeBtn").addClass("portfolio_popup_closeToggle");
  //         }, 500);
  //         // $("body").addClass("full");
  //       }
  //     });
  //   });
  // }
  function gallWrId(e, evt){
    evt.preventDefault();
    var wr_id = "#"+$(e).parent(".gall_li").attr("id");
    var link = $(e).attr("href");
    // console.log(wr_id);
    // console.log(link);
    $(".portpolio_popup").show(0);
      popupView(wr_id, link);
      setTimeout(function() { 
        $(".portfolio_popup_bg, .portfolio_popup_closeBtn").addClass("portfolio_popup_closeToggle");
      }, 500);
    // $(e).click(function (){ 
      // if($(".portpolio_popup").hasClass("done")){
      // $(".portpolio_popup").removeClass("done");
      // return false
      // } else {
      //   $(".portpolio_popup").show(0);
      //   $(".portpolio_popup").addClass("done");
      //   popupView(wr_id, link);
      //   setTimeout(function() { 
      //     $(".portfolio_popup_bg, .portfolio_popup_closeBtn").addClass("portfolio_popup_closeToggle");
      //   }, 500);
      //   }
      // });
  }




    


  $(".portfolio_popup_bg, .portfolio_popup_closeBtn").click(function(){
    if($(this).hasClass("portfolio_popup_closeToggle")){
      $(".portpolio_popup").hide();
      $(".portfolio_view_contents, .portfolio-hp-linkBox").remove();
      $(".portfolio_popup_closeBtn").hide();
      // $("body").removeClass("full");
      $(".portpolio_popup").removeClass("done");
      $(".portfolio_popup_bg, .portfolio_popup_closeBtn").removeClass("portfolio_popup_closeToggle");
    }
  })
</script>


<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == 'copy')
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url+"/move.php";
    f.submit();
}

// 게시판 리스트 관리자 옵션
jQuery(function($){
    $(".btn_more_opt.is_list_btn").on("click", function(e) {
        e.stopPropagation();
        $(".more_opt.is_list_btn").toggle();
    });
    $(document).on("click", function (e) {
        if(!$(e.target).closest('.is_list_btn').length) {
            $(".more_opt.is_list_btn").hide();
        }
    });
});
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->

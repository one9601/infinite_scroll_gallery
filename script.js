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

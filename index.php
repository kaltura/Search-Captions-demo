<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Search Captions</title>
        <header>Search Captions</header>
        <br/>
        <br/>
       <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
       <script type="text/javascript" src="js/ThumbnailRotatorObj.js"></script>
	   <script id="" language="javascript">
			
			var kdp;
			//var serviceUrl = "prod.kaltura.co.cc";
			var serviceUrl = "www.kaltura.com";
			var multiRequestURL = "http://" + serviceUrl + "/api_v3/index.php?service=multirequest&action=null&format=9&callback=?";
            var numPages;
            var currentPage = 1;
            function parseResults(results) {
                console.log(results);
                var resultsHTML = '';
                // loop the results using jQuery.each
                $.each(results.objects, function( inx, value) {
                    resultsHTML += '<li class="clearfix" onclick="showSectionInMovie(this)" value='+formatTime(value.startTime)+'><img  src="http://'+ serviceUrl + '/p/'+value.asset.partnerId+'/sp/'+value.asset.partnerId+'00/thumbnail/entry_id/'+value.asset.entryId+'"  onmouseover="KalturaThumbRotator.start(this)" onmouseout="KalturaThumbRotator.end(this)"><div class="resultsTextContainer">'+value.entry.name+' - '+formatTime(value.startTime)+'<br/>'+value.content+'</div></li>';
                });

                // add our resultsHTML to #items ul
                $("#resultItems").html(resultsHTML);
                $("#results").fadeIn();
                numPages = Math.floor(results.totalCount / 2 )!= 0 ? Math.floor(results.totalCount / 2 ) : 1;
                //console.log(numPages);
                 $("#nextPage").show(); 
                 $("#previousPage").show();
                if (currentPage == numPages)
                {
                   $("#nextPage").hide();  
                }
                if ( currentPage == 1)
                {
                   $("#previousPage").hide();  
                }
			}
			
			var isFirstPlay = true;
			
			var jsCallbackReady = function(kdpName)
			{
				console.log("kdpName = " + kdpName );
				this.kdp = $("#" + kdpName).get(0);
				kdp.addJsListener("mediaReady", "doFirstPlay");
				kdp.addJsListener("playerPlayed", "playerPlaying");
				kdp.addJsListener("playerPaused", "playerPaused");
			}
			
			function doFirstPlay()
			{
				console.log('doFirstPlay');
				isFirstPlay = true;
				kdp.sendNotification("doPlay");
			}

			function playerPlaying() 
			{
				console.log('playerPlaying');
				if(isFirstPlay ) 
				{
					console.log('pauseKdp');
					isFirstPlay = false;
					setTimeout( function() 
					{
						kdp.sendNotification("doPause");
					}, 50);
				}
			}
	
			function showSectionInMovie(obj)
			{				
				timeString = obj.getAttribute("value");
				console.log("Seek to Time: " , timeString);
				// kdp.sendNotification("DO_SEEK", obj.getAttribute("value"));
				var timeArr = timeString.split(':');
				
				console.log("Seek to NUM: " , timeArr);
				timeInSeconds = timeArr[0] * 3600 + timeArr[1] * 60 + timeArr[2];
				
				console.log("Seek to NUM: " , timeInSeconds);
				
				kdp.sendNotification("doSeek", timeInSeconds); //obj.getAttribute("value")
			}
           
            function multiRequest(filterIndex) {
            
                if ( $("#partner_id") != "") {
                    var data = {"1:format":"9",
                        "1:service":"session",
                        "1:action":"startWidgetSession",
                        "1:widgetId":"_"+$("#partner_id").val(), 
                        "2:ks":"{1:result:ks}", 
                        "2:service":"captionsearch_captionassetitem",
                        "2:action":"search","2:format": "9",
						"2:entryFilter:categoriesMatchOr":$("#categories").val(),
						"2:captionAssetItemFilter:contentMultiLikeOr":$("#search_param").val(),
						"2:captionAssetItemFilter:objectType":"KalturaCaptionAssetBaseFilter",
						"2:captionAssetItemFilter:entryIdEqual":$("#entry_id").val(),
                        "2:captionAssetItemPager:objectType":"KalturaFilterPager",
                        "2:captionAssetItemPager:pageSize":"2",
                        "2:captionAssetItemPager:pageIndex":filterIndex};
				console.log(data);		
                    var settings = { 
                        url: multiRequestURL, 
                        dataType: 'json',
                        data : data ,
                        crossDomain:true,
                       success:function (data){ 
					   console.log(data);
					   parseResults(data[1]); 
					   
					   },
                       error: function (jqXHR, textStatus, errorThrown) {console.log(jqXHR , textStatus, errorThrown);}
                    };
                     currentPage = filterIndex;
                     $.ajax( settings );
                }
                
            }
            
            function formatTime(time) {
                var time = time/1000;
                var hours = Math.floor(time/3600);
                var minutes = Math.floor((time - hours*3600)/60);
                var seconds = Math.round((time - minutes*60 - hours*3600)%60);
                if (hours < 10)
                    {
                        hours = "0" + hours;
                    }
                if (minutes < 10)
                    {
                        minutes = "0" + minutes;
                    }
                
                if (seconds < 10)
                    {
                        seconds = "0" + seconds;
                    }
                
                return hours + ":"+minutes+":"+seconds;
            }
            
            $( function() {
                $("#searchBtn").click(function() {multiRequest(1); return false;});
                $("#nextPage").click(function() {multiRequest(currentPage+1); return false;});
                $("#previousPage").click(function() {multiRequest(currentPage-1); return false;});
            });
        </script>
        
    </head>
    <body>
        <?php
        // put your code here
        ?>
        <div class="clearfix">
            <div class="form">
                <label>Partner ID:</label>
                <br/>
                <input type="text" name="partner_id" id="partner_id" value="309" style="display:none"/>
            </div>
            <!-- //Not tested div class="form">
                <label>Categories:</label>
                 <br/>
                <input type="text" name="categories" id="categories" style="display:none"/>
            </div -->
            <div class="form">
                <label>Entry ID:</label>
                 <br/>
                <input type="text" name="entry_id" id="entry_id" value="1_gdmcbimk" style="display:none"/>
            </div>
            <div class="form">
                <label><b>Search captions:</b></label>
                 <br/>
                 <div>
                    <input type="text" name="search_param" id="search_param" value="BATMAN"/>
                    <button id="searchBtn">Search</button>
                 </div>
            </div>
        </div>
        <br/>
        <div class="results" id="results">
            <ul id="resultItems" >
                
            </ul>
        </div>
        <div id="resultsPager">
            <a id="previousPage" href="#">Previous</a>
            <a id="nextPage" href="#">Next</a>
        </div>
		<object id="kdpId" name="kdpId" type="application/x-shockwave-flash" allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" height="333" width="400" bgcolor="#000000" xmlns:dc="http://purl.org/dc/terms/" xmlns:media="http://search.yahoo.com/searchmonkey/media/" rel="media:video" resource="http://www.kaltura.com/index.php/kwidget/cache_st/1315957882/wid/_309/uiconf_id/5674182/entry_id/1_gdmcbimk" data="http://www.kaltura.com/index.php/kwidget/cache_st/1315957882/wid/_309/uiconf_id/5674182/entry_id/1_gdmcbimk"><param name="allowFullScreen" value="true" /><param name="allowNetworking" value="all" /><param name="allowScriptAccess" value="always" /><param name="bgcolor" value="#000000" /><param name="flashVars" value="&" /><param name="movie" value="http://www.kaltura.com/index.php/kwidget/cache_st/1315957882/wid/_309/uiconf_id/5674182/entry_id/1_gdmcbimk" /><a href="http://corp.kaltura.com">video platform</a> <a href="http://corp.kaltura.com/video_platform/video_management">video management</a> <a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a> <a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a> <a rel="media:thumbnail" href="http://cdnbakmi.kaltura.com/p/309/sp/30900/thumbnail/entry_id/1_gdmcbimk/width/120/height/90/bgcolor/000000/type/2"></a> <span property="dc:description" content=""></span><span property="media:title" content="The Dark Knight Rises with captions[HD].mp4"></span> <span property="media:width" content="400"></span><span property="media:height" content="333"></span> <span property="media:type" content="application/x-shockwave-flash"></span> </object>
    </body>
</html>

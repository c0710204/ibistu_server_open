


function load1(i1){
	
	inow=i1;
	if (a[i1]){
		//start=true;
		//$('#'+a[i1][0]).html($('#'+a[i1][0]).html()+'.................<span id="'+a[i1][0]+'len">0</span>');
		
		$.ajax({
			url:a[i1][1],
			success:function(data){
				eval('var data1='+data);
				$('#'+a[i1][0]+'len').html('');
				if(!isNaN(data))
				{
					$('#'+a[i1][0]+'sti').html('成功,共处理'+data1);
				}
				else
				{
					$('#'+a[i1][0]+'sti').html('失败<br>返回信息:'+data);
				}
				last=0;
				load1(i1+1);
				
			}
		})
			
	}
	else
		{
	//		start=false;
		}
	}

function load2(){
	//if (!start)
	//{
	//	setTimeout("load2()",500);
	//}
	//else
	//{
		if (a[inow]){
		$.ajax({
			url:'/api/api.php?table=helper&action=getTableLength&zipmode=num&table_length='+a[inow][0],
			success:function(data){
				eval('var data1='+data);
				var speed=(data1-last)/0.5;
				$('#'+a[inow][0]+'len').html('已处理'+data1+'，速度为：'+speed+'条/秒');
				last=data1;
				setTimeout("load2()",500);
				//load2();
								}
			})
			
		}
		else
			{
			setTimeout("load2()",500);
		//	load2();
			}
//	}
}
function loader_eval(){
	
	 a=[["courselist","\/api\/databaseCopy.php?table=courselist"],
["major","\/api\/databaseCopy.php?table=major"],
["building","\/api\/databaseCopy.php?table=building"],
["course","\/api\/databaseCopy.php?table=course"],
["coursedetail","\/api\/databaseCopy.php?table=coursedetail"],
["classroom","\/api\/databaseCopy.php?table=classroom"],
["college","\/api\/databaseCopy.php?table=college"],
["classtme","\/api\/databaseCopy.php?table=classtime"]];
	for ( var i = 0; i < a.length; i++) {
		$('#list').html($('#list').html()+'<tr> <td id="'+a[i][0]+'">'+a[i][0]+'</td><td  id="'+a[i][0]+'len"> </td><td  id="'+a[i][0]+'sti"> </td></tr>');
		//alert($('#a[i][0]').html());
	}
	//var start=false;

	inow=0;
	last=0;
	load2();
	load1(0);
}
   loader_eval();
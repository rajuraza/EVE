<?php




require_once('../includes/links_cpanel.php');
require_once('../App/and/ver20/includes/notification.function.php');

$action 	=	$_REQUEST['act'];
$emp_id     =   $_SESSION['user_empid'];

switch ($action)
{

	case 'clockInUpdate':
		$postData		 		=	file_get_contents("php://input");
		$postDataArr	 			=	json_decode($postData,true);
		
		$sql 			=	array();
		$data 			=	array();
		$ipAddr =  $_SERVER['REMOTE_ADDR'];
		$macAddr = 'others'; 
		$AttenDate = date("Y-m-d"); 
		$inTime = date("H:i"); 
		$outTime = date("H:i"); 


		$defaultInTime = getDefaultClockInOutTime($emp_id);
		//echo "$macAddr";
		$to_time = strtotime($defaultInTime);
		$from_time = strtotime($inTime);
		//$getDelayOrPriorMinutes = round(($to_time-$from_time) / 60,2);
		$getDelayOrPriorMinutes = round(($from_time-$to_time) / 60,2);

		//echo "$getDelayOrPriorMinutes"."min";

		$getDelayOrPriorMinutes1 = floor($getDelayOrPriorMinutes / 60)." ".'Hrs'." ".($getDelayOrPriorMinutes -   floor($getDelayOrPriorMinutes / 60) * 60);
	
		//echo "$getDelayOrPriorMinutes1"."hrs+min";
		// $actionType = $postDataArr("action"); 
		//--------------------------------------------------//

		$sqlyesclock = array();
                $sqlyesclock['QUERY'] = "SELECT * FROM " . _DB_ACC_EMPLOYEE_ . " 
                                                                     WHERE `id`     =   ? 
                                                                     AND   `status`         =   ?
                                                                     ";

                $sqlyesclock['PARAM'][] = array('FILD' => 'id', 'DATA' => $emp_id, 'TYP' => 's');
                $sqlyesclock['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                $resyesclock = $mycms->sql_select($sqlyesclock);
                $numyesclock = $mycms->sql_numrows($resyesclock);
                if ($numyesclock > 0) {
                	if ($resyesclock['0']['clockInOut']=='yes') {


                	$sql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'status',	'DATA' => 'A',			'TYP' => 's');
		$sql['QUERY'] 	=	"SELECT * FROM ".DB_ACC_CLOCK_IN_OUT_." 
					WHERE  `empId`		=	?
					AND 
					`status`		=	?
					";
		$resPost1 	=	$mycms->sql_select($sql);
		$numPost1	=	$mycms->sql_numrows($resPost1);

		if ($numPost1 > 0) {
			// dkjkgdhgkjdfhj
			if ($resPost1['0']['Ipcheck'] == "others") {
				$sql4 = array();
				$sql4['PARAM'][]	=	array('FILD' => 'macAddress',	'DATA' => $ipAddr,			'TYP' => 's');
				$sql4['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
				$sql4['QUERY'] 	=	"UPDATE ".DB_ACC_CLOCK_IN_OUT_." 
						SET
						`macAddress`		=	?
						WHERE
						`empId`		=	?
						
						
					
						";
				$res4 	=	$mycms->sql_update($sql4);
				if ($res4) {
					$data['clockInOutArr']['alertMessage'] = "You are successfully clocked In";
					$data['clockInOutArr']['alertType'] = "sucess";
					$data['clockInOutArr']['alertTitle'] = "Success";
					if($getDelayOrPriorMinutes1 < 0){
						$data['clockInLateOrPrior']['msg'] = "You are clocking before by ".$getDelayOrPriorMinutes1." minutes";
						}else{
									
						$data['clockInLateOrPrior']['msg'] = "You are late by ".($getDelayOrPriorMinutes1)." minutes";
						$content = date("d-m-Y").' '.'you are'.' '.$getDelayOrPriorMinutes1.' '.'Mins late';
						postWallByCompany($emp_id,$content,"red");
					    }

				}
			}
			// dkjkgdhgkjdfhj
			if (($resPost1['0']['ipAddress'] == $ipAddr)  || ($resPost1['0']['Ipcheck'] == $macAddr)) {
				// jhsdfkjhsdlk

				$checkDateSql = array();
				$checkDateSql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
				$checkDateSql['PARAM'][]	=	array('FILD' => 'date',	'DATA' => $AttenDate,			'TYP' => 's');
				$checkDateSql['QUERY'] 	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_ATTENDENCE." 
							WHERE  `empId`		=	?
							AND 
							`date`			=	?
							";
				$resCheckDate 	=	$mycms->sql_select($checkDateSql);
				$numCheckDate	=	$mycms->sql_numrows($resCheckDate);
				if ($numCheckDate > 0) {
					if ($resCheckDate['0']['intime'] != "" || $resCheckDate['0']['intime'] != null) {
						$data['clockInOutArr']['alertMessage'] = "You have already clocked In";	
						$data['clockInOutArr']['alertType'] = "error";	
						$data['clockInOutArr']['alertTitle'] = "";
						if($getDelayOrPriorMinutes1 < 0){
						$data['clockInLateOrPrior']['msg'] = "You are clocking before by ".$getDelayOrPriorMinutes1." minutes";
						}else{
									
						$data['clockInLateOrPrior']['msg'] = "You are late by ".($getDelayOrPriorMinutes1)." minutes";
						$content = date("d-m-Y").' '.'you are'.' '.$getDelayOrPriorMinutes1.' '.'Mins late';
						postWallByCompany($emp_id,$content,"red");
					    }

					}else{
							// kkkkkkkkkkkkkkkkkkkkk
						$sql 			=	array();
						$sql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'date',	'DATA' => $AttenDate,			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'intime',	'DATA' => $inTime,			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'attenStatus',	'DATA' => 'In',			'TYP' => 's');
						$sql['QUERY'] 	=	"INSERT INTO "._DB_ACC_EMPLOYEE_ATTENDENCE." 
								SET

								`empId`		=	?,
								`date`		=	?,
								`intime`	=	?,
								`attenStatus`	=	?
							
								";
						$res 	=	$mycms->sql_insert($sql);
						if ($res) {

							$data['clockInOutArr']['alertMessage'] = "You are successfully clocked In";
							$data['clockInOutArr']['alertType'] = "sucess";
							$data['clockInOutArr']['alertTitle'] = "Success";
							if($getDelayOrPriorMinutes1 < 0){
						$data['clockInLateOrPrior']['msg'] = "You are clocking before by ".$getDelayOrPriorMinutes1." minutes";
						}else{
									
						$data['clockInLateOrPrior']['msg'] = "You are late by ".($getDelayOrPriorMinutes1)." minutes";
						$content = date("d-m-Y").' '.'you are'.' '.$getDelayOrPriorMinutes1.' '.'Mins late';
						postWallByCompany($emp_id,$content,"red");
					    }
							
						}else{
							$data['clockInOutArr']['alertMessage'] = "Something went wrong";
							$data['clockInOutArr']['alertType'] = "error";
							$data['clockInOutArr']['alertTitle'] = "";
						}
							// kkkkkkkkkkkkkkkkkkkkk
					}
					
				}else{
					$sql 	=	array();
						$sql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
					$sql['PARAM'][]	=	array('FILD' => 'date',	'DATA' => $AttenDate,			'TYP' => 's');
					$sql['PARAM'][]	=	array('FILD' => 'intime',	'DATA' => $inTime,			'TYP' => 's');
					$sql['PARAM'][]	=	array('FILD' => 'attenStatus',	'DATA' => 'In',			'TYP' => 's');
					$sql['QUERY'] 	=	"INSERT INTO "._DB_ACC_EMPLOYEE_ATTENDENCE." 
							SET

							`empId`		=	?,
							`date`		=	?,
							`intime`	=	?,
							`attenStatus`	=	?
						
							";
					$res 	=	$mycms->sql_insert($sql);
					if ($res) {

						$data['clockInOutArr']['alertMessage'] = "You are successfully clocked In";
						$data['clockInOutArr']['alertType'] = "sucess";
						$data['clockInOutArr']['alertTitle'] = "Success";

						if($getDelayOrPriorMinutes1 < 0){
						$data['clockInLateOrPrior']['msg'] = "You are clocking before by ".$getDelayOrPriorMinutes1." minutes";
						}else{
									
						$data['clockInLateOrPrior']['msg'] = "You are late by ".($getDelayOrPriorMinutes1)." minutes";
						$content = date("d-m-Y").' '.'you are'.' '.$getDelayOrPriorMinutes1.' '.'Mins late';
						postWallByCompany($emp_id,$content,"red");
					    }
					}else{
						$data['clockInOutArr']['alertMessage'] = "Something went wrong";
						$data['clockInOutArr']['alertType'] = "error";
						$data['clockInOutArr']['alertTitle'] = "";
				     }
			    }
				// jhsdfkjhsdlk
			}else{
				$data['clockInOutArr']['alertMessage'] = "You are from different IP address";	
				$data['clockInOutArr']['alertType'] = "error";	
				$data['clockInOutArr']['alertTitle'] = "";	
			}
		}else{
			$data['clockInOutArr']['alertMessage'] = "You have no permission to clock in on web ";
			$data['clockInOutArr']['alertType'] = "error";
			$data['clockInOutArr']['alertTitle'] = "";
		}
                	
                	}else{
                		$data['clockInOutArr']['alertMessage'] = "You have no permission to clock in on web";
				$data['clockInOutArr']['alertType'] = "error";
				$data['clockInOutArr']['alertTitle'] = "";

                	}

                    
                } 
		//--------------------------------------------------//

		
		
		echo json_encode($data);
	break;
	case 'clockOutUpdate':
		$postData		 		=	file_get_contents("php://input");
		$postDataArr	 			=	json_decode($postData,true);
		$sql 			=	array();
		$data 			=	array();
		$ipAddr =  $_SERVER['REMOTE_ADDR'];
		$macAddr ='others'; 
		$AttenDate = date("Y-m-d"); 
		$outTime = date("H:i"); 
		// $actionType = $postDataArr("action"); 


		$sql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'status',	'DATA' => 'A',			'TYP' => 's');
		$sql['QUERY'] 	=	"SELECT * FROM ".DB_ACC_CLOCK_IN_OUT_." 
					WHERE  `empId`		=	?
					AND 
					`status`		=	?
					";
		$resPost1 	=	$mycms->sql_select($sql);
		$numPost1	=	$mycms->sql_numrows($resPost1);

		if ($numPost1 > 0) {

			// dkjkgdhgkjdfhj
			if ($resPost1['0']['Ipcheck'] == "others" AND $resPost1['0']['macAddress'] != "" ) {
				$sql5 = array();
				$sql5['PARAM'][]	=	array('FILD' => 'clockOutIP',	'DATA' => $ipAddr,			'TYP' => 's');
				$sql5['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
				$sql5['QUERY'] 	=	"UPDATE ".DB_ACC_CLOCK_IN_OUT_." 
						SET
						`clockOutIP`		=	?
						WHERE
						`empId`		=	?
						
						
					
						";
				$res4 	=	$mycms->sql_update($sql5);
				if ($res4) {
					$data['clockInOutArr']['alertMessage'] = "You are successfully clocked OUT";
					$data['clockInOutArr']['alertType'] = "sucess";
					$data['clockInOutArr']['alertTitle'] = "Success";
				}
			}
			// dkjkgdhgkjdfhj
			if (($resPost1['0']['ipAddress'] == $ipAddr)  || ($resPost1['0']['Ipcheck'] == $macAddr)) {
				// jhsdfkjhsdlk
				
				$checkDateSql = array();
				$checkDateSql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
				$checkDateSql['PARAM'][]	=	array('FILD' => 'date',	'DATA' => $AttenDate,			'TYP' => 's');
				$checkDateSql['QUERY'] 	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_ATTENDENCE." 
							WHERE  `empId`		=	?
							AND 
							`date`			=	?
							";
				$resCheckDate 	=	$mycms->sql_select($checkDateSql);
				$numCheckDate	=	$mycms->sql_numrows($resCheckDate);
				if ($numCheckDate > 0) {
					if ($resCheckDate['0']['outTime'] != "" || $resCheckDate['0']['outTime'] != null) {
						$data['clockInOutArr']['alertMessage'] = "You have already clocked out";	
						$data['clockInOutArr']['alertType'] = "error";	
						$data['clockInOutArr']['alertTitle'] = "";	
					}else{
							// kkkkkkkkkkkkkkkkkkkkk
						$sql 			=	array();
						
						$sql['PARAM'][]	=	array('FILD' => 'outTime',	'DATA' => $outTime,			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'attenStatus',	'DATA' => 'Out',			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'empId',	'DATA' => $emp_id,			'TYP' => 's');
						$sql['PARAM'][]	=	array('FILD' => 'date',	'DATA' => $AttenDate,			'TYP' => 's');
						$sql['QUERY'] 	=	"UPDATE "._DB_ACC_EMPLOYEE_ATTENDENCE." 
								SET

								`outTime`	=	?,
								`attenStatus`	=	?

								WHERE `empId`		=	?
								AND
								`date`		=	?
							
								";
						$res 	=	$mycms->sql_update($sql);
						if ($res) {

							$data['clockInOutArr']['alertMessage'] = "You are successfully clocked out";
							$data['clockInOutArr']['alertType'] = "sucess";
							$data['clockInOutArr']['alertTitle'] = "Success";
						}else{
							$data['clockInOutArr']['alertMessage'] = "Something went wrong";
							$data['clockInOutArr']['alertType'] = "error";
							$data['clockInOutArr']['alertTitle'] = "";
						}
							// kkkkkkkkkkkkkkkkkkkkk
					}
					
				}else{

					$data['clockInOutArr']['alertMessage'] = "Please clocked In first";
					$data['clockInOutArr']['alertType'] = "sucess";
					$data['clockInOutArr']['alertTitle'] = "Success";
				}
				// jhsdfkjhsdlk
			}else{
				$data['clockInOutArr']['alertMessage'] = "You are from different IP address";	
				$data['clockInOutArr']['alertType'] = "error";	
				$data['clockInOutArr']['alertTitle'] = "";	
			}
		}else{
			$data['clockInOutArr']['alertMessage'] = "You have no permission to clock out on web ";
			$data['clockInOutArr']['alertType'] = "error";
			$data['clockInOutArr']['alertTitle'] = "";
		}
		
		echo json_encode($data);
	break;
	case 'getEmployeeWallAccess' :
		$postData		 			=	file_get_contents("php://input");
		$postDataArr	 			=	json_decode($postData,true);
		$result						=	array();

		
		$sqlPost1 					=	array();
		$sqlPost1['QUERY']			=	"SELECT * FROM "._DB_EMPLOYEE_WALL_ACCESS_."
												";
		$resPost1 					=	$mycms->sql_select($sqlPost1);
		$numPost1 					=	$mycms->sql_numrows($resPost1);
		if($numPost1==0){
			$sqlDetails				=	array();
			$sqlDetails['QUERY']	=	"SELECT 
											* FROM 
											"._DB_ACC_EMPLOYEE_." 
											WHERE `status`	!=	?";

			$sqlDetails['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 	'TYP' => 's');
			$resDetails				=	$mycms->sql_select($sqlDetails);
			foreach ($resDetails as $keys => $values) {
				$typeInsert             =   array();
                $typeInsert['QUERY']    =   "INSERT INTO "._DB_EMPLOYEE_WALL_ACCESS_."
                                                        SET 
                                                        `empId`             =   ?,
                                                        `wallAccess`        =   ?,
                                                        `createdDateTime`   =   ?,
                                                        `createdSession`    =   ?,
                                                        `createdIp`         =   ?
                                                        ";
                $typeInsert['PARAM'][]  =   array('FILD' => 'empId' ,               'DATA' => $values['id'] ,               	'TYP' => 's');
                $typeInsert['PARAM'][]  =   array('FILD' => 'wallAccess',           'DATA' => 'Yes',                           'TYP' => 's');
                $typeInsert['PARAM'][]  =   array('FILD' => 'createdDateTime' ,     'DATA' => date('Y-m-d H:i:s') ,             'TYP' => 's');
                $typeInsert['PARAM'][]  =   array('FILD' => 'createdSession' ,      'DATA' => session_id() ,                    'TYP' => 's');
                $typeInsert['PARAM'][]  =   array('FILD' => 'createdIp' ,           'DATA' => $_SERVER['REMOTE_ADDR'] ,         'TYP' => 's');
                $resTypeInsert          =   $mycms->sql_insert($typeInsert);
			}
		}

		$sqlPost 					=	array();
		$sqlPost['QUERY']			=	"SELECT * FROM "._DB_EMPLOYEE_WALL_ACCESS_."
												WHERE  `empId`		=	?
												AND    `wallAccess`	=	?
												AND    `status`	 	=	?";

		$sqlPost['PARAM'][]			=	array('FILD' => 'empId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlPost['PARAM'][]			=	array('FILD' => 'wallAccess', 	'DATA' => 'yes', 						'TYP' => 's');
		$sqlPost['PARAM'][]			=	array('FILD' => 'status', 	 	'DATA' => 'A', 							'TYP' => 's');
		$resPost 					=	$mycms->sql_select($sqlPost);
		$numPost 					=	$mycms->sql_numrows($resPost);
		$rowPost 					=	$resPost[0];
		if($numPost){
			$result['accessToPost1']=	$rowPost['wallAccess'];
			$result['accessToPost']	=	'yes';
		} else {
			$result['accessToPost']	=	'no';
		}
		echo json_encode($result);
	break;
	case 'add':
	
	    $postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$tagFriendID 		=	implode(",", $postDataArr['tagFriend']);
		$tagFriendArrSize 	=	count($postDataArr['tagFriend']);

		$sql 			=	array();

		$sql['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $postDataArr['empid'],				'TYP' => 's');

		if($postDataArr['youtubeLink']=='yes'){
			$linkArr 				=	explode(" ", $postDataArr['content']);
			if(strpos($postDataArr['content'],"youtu.be")){
				$youtubeLinkArray		=	explode("/", $linkArr[0]);
				$videoId				=	$youtubeLinkArray[3];
				$postDataArr['content']	=	"https://www.youtube.com/watch?v=".$videoId;

				unset($linkArr[0]);
				$contentString 			=	implode(" ", $linkArr);
				
			} else {
				$postDataArr['content']	=	$linkArr[0];
				unset($linkArr[0]);
				$contentString 			=	implode(" ", $linkArr);
			}

			$sql['PARAM'][]	=	array('FILD' => 'content',		'DATA' => $contentString,		'TYP' => 's');
		}else if($postDataArr['isLink']=='yes'){
			$needle 	=	"http://";
			if(strpos($postDataArr['content'],$needle)==false){
				$postDataArr['content'] 	=	'http://'.$postDataArr['content'];
			}

			$title = getTitle($postDataArr['content']);

			$newContent 	=	$title.'<br><br><br><a href="'.$postDataArr['content'].'" target="_blank">'.$postDataArr['content'].'</a>';

			$metaContent 	=	get_meta_tags($postDataArr['content']);

			function getTitle($url) {
			 	$data = file_get_contents($url);
			    $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
			    return $title;
			}

			$sql['PARAM'][]	=	array('FILD' => 'content',		    'DATA' => $newContent,	            'TYP' => 's');
		}else {

			$sql['PARAM'][]	=	array('FILD' => 'content',		    'DATA' => $postDataArr['content'],	            'TYP' => 's');
		}
		$sql['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',									'TYP' => 's');
		
		if($tagFriendID!=''){
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => 'tag',		'TYP' => 's');
		}else if($postDataArr['privacyStatus']!=''){
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => $postDataArr['privacyStatus'],		'TYP' => 's');
		}else {
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',		'TYP' => 's');
		}
		
		
		if($postDataArr['typemode']=='media' || $postDataArr['youtubeLink']=='yes'){
			$where 	=	'`media`   = ?,';

			$sql['PARAM'][]	=	array('FILD' => 'media',		'DATA' => "yes",		'TYP' => 's');
			//$sql['PARAM'][]	=	array('FILD' => 'mediaType',	'DATA' => $postDataArr['mediatype'],				'TYP' => 's');

		}

		/*if($postDataArr['youtubeLink']=='yes'){

			$where 	=	'`media`   = ?,`mediaType`   = ?,';

			$sql['PARAM'][]	=	array('FILD' => 'media',		'DATA' => $postDataArr['content'],		'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'mediaType',	'DATA' => 'youtubeLink',				'TYP' => 's');
		}*/
		
		if($postDataArr['appliedcolour']==''){

			$postDataArr['appliedcolour']	=	'none';
		}
		
		$sql['QUERY']	=	"INSERT INTO "._DB_ACC_WALL."
								SET 
									`employeeId`  		= 	?,
									`content`			=	?,
									`status`			=	?,
									`privacyStatus`		=	?,
									".$where."
									`tagFriend`			=	?,
									`groupId`			=	?,
									`colourApplied`		=	?,
									`createdDate`		=	?,
									`createdSession`	=	?,
									`createdIp`			=	?";
		
		
		$sql['PARAM'][]	=	array('FILD' => 'tagFriend',		'DATA' => $tagFriendID,							'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'groupId',			'DATA' => $postDataArr['groupId'],				'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'colourApplied',	'DATA' => $postDataArr['appliedcolour'],		'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'created_date',		'DATA' => date('Y-m-d H:i:s'),					'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'created_session',	'DATA' => session_id(),							'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'created_ip',		'DATA' => $_SERVER['REMOTE_ADDR'],				'TYP' => 's');
		//print_r($sql);die();
		$res 			=	$mycms->sql_insert($sql);

		if($postDataArr['typemode']=='media') {
			$fileNameArr 	=	$postDataArr['filename'];
			$fileTypeArr 	=	$postDataArr['filetype'];
			foreach ($fileNameArr as $keyNameArr => $valueNameArr) {
				$fileName 	=	$valueNameArr;
				$fileType 	=	$fileTypeArr[$keyNameArr];

				$sqlMediaInsert 			=	array();
				$sqlMediaInsert['QUERY']	=	"INSERT INTO "._DB_ACC_WALL_MEDIA."
													SET 
													 	`wallId`				=	?,
													 	`media`					=	?,
													 	`mediaType`				=	?,
													 	`createdDate`			=	?,
													 	`createdSession`		=	?,
													 	`createdIp`				=	?";

				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'wallId',				'DATA' => $res,								'TYP' => 's');
				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'media',				'DATA' => $fileName,						'TYP' => 's');
				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'mediaType',			'DATA' => $fileType,						'TYP' => 's');
				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdDate',			'DATA' => date("Y-m-d H:i:s"),				'TYP' => 's');
				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdSession',		'DATA' => session_id(),						'TYP' => 's');
				$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdIp',			'DATA' => $_SERVER['REMOTE_ADDR'],			'TYP' => 's');

				$resMediaInsert 			=	$mycms->sql_insert($sqlMediaInsert);
				$fileUrl  = "../upload/post/".$fileName;

				compress_image($fileUrl,$fileUrl,'web');

			}
		} else if ($postDataArr['youtubeLink']=='yes') {
			$fileName 	=	$postDataArr['content'];
			$fileType 	=	"youtubeLink";

			$sqlMediaInsert 			=	array();
			$sqlMediaInsert['QUERY']	=	"INSERT INTO "._DB_ACC_WALL_MEDIA."
												SET 
												 	`wallId`				=	?,
												 	`media`					=	?,
												 	`mediaType`				=	?,
												 	`createdDate`			=	?,
												 	`createdSession`		=	?,
												 	`createdIp`				=	?";

			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'wallId',				'DATA' => $res,								'TYP' => 's');
			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'media',				'DATA' => $fileName,						'TYP' => 's');
			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'mediaType',			'DATA' => $fileType,						'TYP' => 's');
			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdDate',			'DATA' => date("Y-m-d H:i:s"),				'TYP' => 's');
			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdSession',		'DATA' => session_id(),						'TYP' => 's');
			$sqlMediaInsert['PARAM'][]	=	array('FILD' => 'createdIp',			'DATA' => $_SERVER['REMOTE_ADDR'],			'TYP' => 's');

			$resMediaInsert 			=	$mycms->sql_insert($sqlMediaInsert);
		}

		if($tagFriendArrSize>0){

			$getNotification		=	notificationDetails('13');
			$notificationSubject	=	$getNotification['subject'];
			$notificationBody		=	$getNotification['body'];

			$getEmpDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
			$getEmpName 			=	$getEmpDetails['name'];
			$gender 				=	(strtolower($getEmpDetails['gender'])=='male')?'his':'her';

			$dataArray				=	array('{name}','{gender}');
			$replaceDataArray		=	array(ucwords($getEmpName),$gender);

			$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);

			foreach ($postDataArr['tagFriend'] as $keyTagFriend => $valueTagFriend) {
				send_notification($valueTagFriend, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Tag a Friend", $res);
				$device_id 				=	$_SERVER['REMOTE_ADDR'];

				$fcm_token 				= 	get_fcm_id($valueTagFriend);

	            foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
	                send_fcm_notification($valueTagFriend,$_SESSION['user_empid'] , $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Post Tag", "Post Tag", $res);
	            }
			}

		}

		$result 		=	array();
		if($res){
			$result['Status']	=	'Success';
		}
		else{
			$result['Status']	=	'Failed';
		}
	
	  	echo json_encode($result);

	    break;

	case 'edit':
	    $postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
//		print_r($postDataArr);
//		echo "%%%". $postDataArr['previousMediaArray'];



		$tagFriendID 		=	implode(",", $postDataArr['tagFriend']);
		$tagFriendArrSize 	=	count($postDataArr['tagFriend']);

		$fileSize	=	sizeof($postDataArr['filename']);
		$previousMedia	=	sizeof($postDataArr['previousMediaArray']);

		$updatedData	=	"";
		$updatedData	.=	" `content`				=	?,
								`tagFriend`			=	?,
								`colourApplied`		=	?,
								`privacyStatus`		=	?,
								`modifiedDate`		=	?,
								`modifiedSession`	=	?,
								`modifiedIp`		=	?,
								`media`				=	?";
		if ($postDataArr['youtubeLink']=='yes') {
			$explodeContent		=	explode(" ", $postDataArr['content']);
			if (strpos($explodeContent[0], "youtube.com") && strpos($explodeContent[0], "?v=")) {
				$postContent	=	$explodeContent[1];
				$postMedia		=	$explodeContent[0];
			} else if (strpos($explodeContent[1], "youtube.com") && strpos($explodeContent[1], "?v=")) {
				$postContent	=	$explodeContent[0];
				$postMedia		=	$explodeContent[1];
			}
			$isMedia	=	'yes';
		} else {
			// $isMedia		=	'';
			$postContent	=	$postDataArr['content'];
		}
		if ($postDataArr['youtubeLink']=='no') {
			if (sizeof($postDataArr['tagFriend'])>0) {
				$tagFriendString	=	implode(",", $postDataArr['tagFriend']);
				$privacyStatus		=	"tag";
			} else {
				$tagFriendString	=	"";
				$privacyStatus		=	$postDataArr['privacyStatus'];
			}
			if (sizeof($postDataArr['filename'])) {
				$isMedia	=	'yes';
			} else {
				$isMedia	=	'';
			}
			if (sizeof($postDataArr['previousMediaArray'])>0) {
				$isMedia	=	'yes';
			}
		}

		if ($postDataArr['appliedcolour']!="") {
			$appliedColour	=	$postDataArr['appliedcolour'];
		} else {
			$appliedColour	=	'none';
		}

		$sqlWallUpdate				=	array();
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'content',		    	'DATA' => $postContent,	            	'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'tagFriend',		    'DATA' => $tagFriendString,	            'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'colourApplied',		'DATA' => $appliedColour,	            'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'privacyStatus',		'DATA' => $privacyStatus,	            'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'modifiedDate',		    'DATA' => date('Y-m-d H:i:s'),	'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'modifiedSession',		'DATA' => session_id(),	            	'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'modifiedIp',		    'DATA' => $_SERVER['REMOTE_ADDR'],	    'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'media',		    	'DATA' => $isMedia,	    				'TYP' => 's');
		$sqlWallUpdate['PARAM'][]	=	array('FILD' => 'id',		    		'DATA' => $postDataArr['postId'],	    'TYP' => 's');
		$sqlWallUpdate['QUERY']		=	"UPDATE "._DB_ACC_WALL."
											SET 
												".$updatedData."
											WHERE `id`	=	?";
		$resWallUpdate				=	$mycms->sql_update($sqlWallUpdate);
		if($resWallUpdate){
			if ($previousMedia>0) {
				$mediaIdString = implode("','", $postDataArr['previousMediaArray']);
//				echo "###" . "update";
				$sqlWallMediaUpdate = array();
				$sqlWallMediaUpdate['QUERY'] = "UPDATE " . _DB_ACC_WALL_MEDIA . "
														SET
															`status`	=	?
														WHERE `wallId`	=	?
															AND `id` NOT IN ('" . $mediaIdString . "')";
				$sqlWallMediaUpdate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
				$sqlWallMediaUpdate['PARAM'][] = array('FILD' => 'wallId', 'DATA' => $postDataArr['postId'], 'TYP' => 's');
				$mycms->sql_update($sqlWallMediaUpdate);

			}
			if ($previousMedia == 0) {
//				$mediaIdString = implode("','", $postDataArr['previousMediaArray']);
//				echo "###" . "update";
				$sqlWallMediaUpdate = array();
				$sqlWallMediaUpdate['QUERY'] = "UPDATE " . _DB_ACC_WALL_MEDIA . "
														SET
															`status`	=	?
														WHERE `wallId`	=	?";

				$sqlWallMediaUpdate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');
				$sqlWallMediaUpdate['PARAM'][] = array('FILD' => 'wallId', 'DATA' => $postDataArr['postId'], 'TYP' => 's');
				$mycms->sql_update($sqlWallMediaUpdate);

			}


		}
		if($resWallUpdate){

				foreach ($postDataArr['filename'] as $keyFile => $fileName) {
//					echo "###" . "insert";
					$sqlWallMedia = array();
					$sqlWallMedia['QUERY'] = "INSERT INTO " . _DB_ACC_WALL_MEDIA . "
														SET 
															`wallId`			=	?,
															`media`				=	?,
															`mediaType`			=	?,
															`createdDate`		=	?,
															`createdSession`	=	?,
															`createdIp`			=	?,
															`modifiedDate`		=	?,
															`modifiedSession`	=	?,
															`modifiedIp`		=	?";
					$sqlWallMedia['PARAM'][] = array('FILD' => 'wallId', 				'DATA' => $postDataArr['postId'], 					'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'media',			 		'DATA' => $fileName, 								'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'mediaType', 			'DATA' => $postDataArr['mediatype'][$keyFile], 	    'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'createdDate', 			'DATA' => date('Y-m-d H:s:i'), 			 	'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'createdSession', 		'DATA' => session_id(), 						  	'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'createdIp', 			'DATA' => $_SERVER['REMOTE_ADDR'], 				  	'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'modifiedDate', 			'DATA' => date('Y-m-d H:s:i'), 			  	'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'modifiedSession', 		'DATA' => session_id(), 							'TYP' => 's');
					$sqlWallMedia['PARAM'][] = array('FILD' => 'modifiedIp', 			'DATA' => $_SERVER['REMOTE_ADDR'], 					'TYP' => 's');
					$mycms->sql_insert($sqlWallMedia);

				}
		}


		if($resWallUpdate){
			if($postDataArr['youtubeLink']=='yes') {
				$sqlWallMediaUpdate				=	array();
				$sqlWallMediaUpdate['QUERY']	=	"UPDATE "._DB_ACC_WALL_MEDIA."
														SET 
															`status`	=	?
														WHERE `wallId`	=	?";
				$sqlWallMediaUpdate['PARAM'][]	=	array('FILD' => 'status',		    	'DATA' => 'D',	            			'TYP' => 's');
				$sqlWallMediaUpdate['PARAM'][]	=	array('FILD' => 'wallId',		    	'DATA' => $postDataArr['postId'],	    'TYP' => 's');
				$mycms->sql_update($sqlWallMediaUpdate);

				$sqlWallMedia			=	array();
				$sqlWallMedia['QUERY']	=	"INSERT INTO "._DB_ACC_WALL_MEDIA."
														SET 
															`wallId`			=	?,
															`media`				=	?,
															`mediaType`			=	?,
															`createdDate`		=	?,
															`createdSession`	=	?,
															`createdIp`			=	?,
															`modifiedDate`		=	?,
															`modifiedSession`	=	?,
															`modifiedIp`		=	?";
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'wallId',		    	'DATA' => $postDataArr['postId'],	            'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'media',		    	'DATA' => $postMedia,	            			'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'mediaType',		    'DATA' => 'youtubeLink',						'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'createdDate',		    'DATA' => date('Y-m-d H:s:i'),	        'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'createdSession',		'DATA' => session_id(),	            			'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'createdIp',		    'DATA' => $_SERVER['REMOTE_ADDR'],	            'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'modifiedDate',		    'DATA' => date('Y-m-d H:s:i'),	        'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'modifiedSession',		'DATA' => session_id(),	            			'TYP' => 's');
				$sqlWallMedia['PARAM'][]	=	array('FILD' => 'modifiedIp',		    'DATA' => $_SERVER['REMOTE_ADDR'],	            'TYP' => 's');
				$mycms->sql_insert($sqlWallMedia);
			}
		}



		$oldTagFriendIDArr 			=	explode(",", $postDataArr['oldTagFriendId']);
		if($tagFriendArrSize>0){
			$getNotification		=	notificationDetails('13');
			$notificationSubject	=	$getNotification['subject'];
			$notificationBody		=	$getNotification['body'];
			$getEmpDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
			$getEmpName 			=	$getEmpDetails['name'];
			$gender 				=	(strtolower($getEmpDetails['gender'])=='male')?'his':'her';
			$dataArray				=	array('{name}','{gender}');
			$replaceDataArray		=	array(ucwords($getEmpName),$gender);
			$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);
			foreach ($postDataArr['tagFriend'] as $keyTagFriend => $valueTagFriend) {
				if (!in_array($valueTagFriend, $oldTagFriendIDArr)) {
					send_notification($valueTagFriend, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Tag a Friend", $postDataArr['postId']);
					$device_id 				=	$_SERVER['REMOTE_ADDR'];

					$fcm_token 				= 	get_fcm_id($valueTagFriend);

					foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
						send_fcm_notification($valueTagFriend,$_SESSION['user_empid'] , $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Post Tag", "Post Tag", $postDataArr['postId']);
					}
				}
			}
		}


		/*if($postDataArr['youtubeLink']=='yes'){
			$sql['PARAM'][]	=	array('FILD' => 'content',		    'DATA' => '',	            					'TYP' => 's');
		}
		else {
			$sql['PARAM'][]	=	array('FILD' => 'content',		    'DATA' => $postDataArr['content'],	            'TYP' => 's');
		}
		if($tagFriendID!=''){
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => 'tag',		'TYP' => 's');
		}
		else if($postDataArr['privacyStatus']!=''){
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => $postDataArr['privacyStatus'],		'TYP' => 's');
		}
		else {
			$sql['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',		'TYP' => 's');
		}
		if($postDataArr['youtubeLink']=='yes'){
			$where 	=	'`media`   = ?,`mediaType`   = ?,';
			$sql['PARAM'][]	=	array('FILD' => 'media',		'DATA' => $postDataArr['content'],		'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'mediaType',	'DATA' => 'youtubeLink',				'TYP' => 's');
		}
		
		if($postDataArr['typemode']=='media'){
			$where 	=	'`media`   = ?,`mediaType`   = ?,';
			$sql['PARAM'][]	=	array('FILD' => 'media',		'DATA' => $postDataArr['filename'],		'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'mediaType',	'DATA' => $postDataArr['mediatype'],	'TYP' => 's');

		}
		if($postDataArr['hasMedia']=='false'){
			$where 	=	'`media` = ?, 
						 `mediaType` = ?,';
			$sql['PARAM'][]	=	array('FILD' => 'media',		'DATA' => "",		'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'mediaType',	'DATA' => "",		'TYP' => 's');
		}
		if($postDataArr['appliedcolour']==''){
			$postDataArr['appliedcolour']	=	'none';
		}
		$oldTagFriendIDArr 			=	explode(",", $postDataArr['oldTagFriendId']);
		if($tagFriendArrSize>0){
			$getNotification		=	notificationDetails('13');
			$notificationSubject	=	$getNotification['subject'];
			$notificationBody		=	$getNotification['body'];

			$getEmpDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
			$getEmpName 			=	$getEmpDetails['name'];
			$gender 				=	(strtolower($getEmpDetails['gender'])=='male')?'his':'her';

			$dataArray				=	array('{name}','{gender}');
			$replaceDataArray		=	array(ucwords($getEmpName),$gender);

			$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);

			foreach ($postDataArr['tagFriend'] as $keyTagFriend => $valueTagFriend) {
				if (!in_array($valueTagFriend, $oldTagFriendIDArr)) {
					send_notification($valueTagFriend, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Tag a Friend", $postDataArr['postId']);
					$device_id 				=	$_SERVER['REMOTE_ADDR'];

					$fcm_token 				= 	get_fcm_id($valueTagFriend);

		            foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
		                send_fcm_notification($valueTagFriend,$_SESSION['user_empid'] , $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Post Tag", "Post Tag", $postDataArr['postId']);
		            }
				}
			}
		}
		
		$sql['QUERY']	=	"UPDATE "._DB_ACC_WALL."
								SET 
									`content`			=	?,
									`privacyStatus`		=	?,
									".$where."
									`tagFriend`			=	?,
									`colourApplied`		=	?,
									`modifiedDate`		=	?,
									`modifiedSession`	=	?,
									`modifiedIp`		=	?
							 WHERE  `id`				=	?";
		$sql['PARAM'][]	=	array('FILD' => 'tagFriend',		'DATA' => $tagFriendID,							'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'colourApplied',	'DATA' => $postDataArr['appliedcolour'],		'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedDate',		'DATA' => date('Y-m-d H:i:s'),					'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedSession',	'DATA' => session_id(),							'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedIp',		'DATA' => $_SERVER['REMOTE_ADDR'],				'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $postDataArr['postId'],				'TYP' => 's');
		//print_r($sql);
		$res 			=	$mycms->sql_update($sql);*/
		$result 		=	array();
		if($resWallUpdate) {
			$result['Status']	=	'Success';
		} else {
			$result['Status']	=	'Failed';
		}

	  	echo json_encode($result);
	break;

	case 'getUpdatedData':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$data 			=	array();

		$sqlFetchData			=	array();
		$sqlFetchData['QUERY']	=	"SELECT * FROM "._DB_ACC_WALL."
											WHERE  `id`		=	?";

		$sqlFetchData['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $postDataArr['postId'], 'TYP' => 's');
		$resFetchData				=	$mycms->sql_select($sqlFetchData);
		
		$rowFetchData				=	$resFetchData[0];

		$data['result']				=	'success';
		$data['postid']				=	$rowFetchData['id'];

		$getCommentCreatorDetails	=	getEmployeeDetails1($rowFetchData['employeeId']);

		$data['creatorName']		=	$getCommentCreatorDetails['name'];
		$data['creatorImage']		=	checkFileExist("../upload/userprofilepicture/",$getCommentCreatorDetails['image']);
		$data['content']			=	$rowFetchData['content'];

		$mediaAll 					=	getWallMedia($rowFetchData['id']);

		/*$checkUnserilize 			=	@unserialize($rowFetchData['media']);
		if ($checkUnserilize!=false) {
			$postMedia 					=	unserialize($rowFetchData['media']);
			$postMediaLength			=	sizeof($postMedia);
			foreach ($postMedia as $key => $postMediaValue) {
				
				$data['media'][$key]['mediaImg']	=	$postMediaValue;
			}
			$data['mediaLength']		=	$postMediaLength;
		} else {
			$postMedia 					=	$rowFetchData['media'];
			//$postMediaLength			=	sizeof($postMedia);
			$data['media'][0]['mediaImg']	=	$postMedia;
			$data['mediaLength']		=	"1";
		}*/

		if ($mediaAll) {
			foreach ($mediaAll as $keyMediaAll => $valueMediaAll) {
				$splitMediaType 	=	explode("/", $valueMediaAll['mediaType']);
				$data['media'][$keyMediaAll]['mediaImg']		=	$valueMediaAll['media'];
				$data['media'][$keyMediaAll]['mediaType']		=	$splitMediaType[0];
				$data['media'][$keyMediaAll]['mediaTypeAll']	=	$valueMediaAll['mediaType'];
			}
			$data['mediaLength']		=	sizeof($mediaAll);
		} else {
			$data['media']				=	array();
			$data['mediaLength']		=	"0";
		}
		
		//$data['mediaType']			=	$rowFetchData['mediaType'];
		/*if($rowFetchData['media']!=""){

			$data['ismedia']		=	'yes';
		}
		else {

			$data['ismedia']		=	'no';
		}*/
		$data['tagFriend']			=	$rowFetchData['tagFriend'];
		$data['colourApplied']		=	$rowFetchData['colourApplied'];
		$data['timeBefore']			=	calculateTime($rowFetchData['createdDate']);

		if($rowFetchData['employeeId']==$_SESSION['user_empid']){

			$data['isDelete']		=	'yes';
		}
		else {

			$data['isDelete']		=	'no';
		}
		if($rowFetchData['mediaType']=='youtubeLink'){

			$youtubeMedia 				=	$rowFetchData['media'];
			$linkCode 					=	substr($youtubeMedia, strpos($youtubeMedia, "=") + 1);

			$data['embed']				=	$linkCode;
		}
		else {

			$data['embed']				=	"";
		}

		$likeDetails				=	getCommentAndLikeCounter($postDataArr['postId']);
		//print_r($likeDetails);
		$data['yourLike']			=	$likeDetails['yourLike'];
		$data['likeCounter']		=	$likeDetails['likeCounter'];

		$data['yourComment']		=	$likeDetails['yourComment'];
		$data['commentCounter']		=	$likeDetails['commentCounter'];

		$commentDetails				=	getAllCommentPostWise($postDataArr['postId']);


		if($rowFetchData['tagFriend']!=''){
			$tagFriendArr 		=	explode(",", $rowFetchData['tagFriend']);
			$tagFriendIdCount 	=	count($tagFriendArr);

			$tagFriendDetails					=	getEmployeeDetails1($tagFriendArr[0]);
			$tagFriendName 						=	$tagFriendDetails['name'];

			if($tagFriendIdCount>2){
				$otherValue 	=	"Others";
			} else {
				$otherValue 	=	"Other";
			}
			if (in_array($_SESSION['user_empid'], $tagFriendArr)) {
				$data['tag_friend']				=	'You';
				$data['tag_friend_id']			=	$_SESSION['user_empid'];
				if($tagFriendIdCount>1){
					$data['tag_friend_other_count']	=	$tagFriendIdCount-1;
					$data['tag_friend_other']			=	$otherValue;
					foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
						//if ($keyFriendArr!=0) {
						if ($valueFriendArr!=$_SESSION['user_empid']) {
							$tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
							$data['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
							$data['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
						}
					}
				}

			}
			else{
				$data['tag_friend']					=	$tagFriendName;
				$data['tag_friend_id']				=	$tagFriendArr[0];
				if($tagFriendIdCount>1){
					$data['tag_friend_other_count']	=	$tagFriendIdCount-1;
					$data['tag_friend_other']			=	$otherValue;
					foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
						if ($keyFriendArr!=0) {
							$tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
							$data['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
							$data['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
						}
					}
				}
			}
		}
		else{

			$data['tag_friend']	=	"";
		}


		foreach ($commentDetails['comment'] as $keyNew => $commentValue) {
			
			$data['comment'][$keyNew]['id']					=	$commentValue['id'];
			$data['comment'][$keyNew]['empId']				=	$commentValue['empId'];
			$data['comment'][$keyNew]['body']				=	$commentValue['body'];
			$data['comment'][$keyNew]['empName']			=	$commentValue['empName'];
			$data['comment'][$keyNew]['empImg']				=	$commentValue['empImg'];
			$data['comment'][$keyNew]['deleteAccess']		=	$commentValue['deleteAccess'];
			$data['comment'][$keyNew]['editAccess']			=	$commentValue['editAccess'];
		}

		echo json_encode($data);

	break;

	case 'getNextPostImage':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$result 		=	array();

		$postId 		=	$postDataArr['postid'];
		$postImagePos	=	$postDataArr['imagePosition'];

		$sqlFetchData				=	array();
		$sqlFetchData['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
												WHERE  `id`		=	?";

		$sqlFetchData['PARAM'][]	=	array('FILD' => 'id', 	'DATA' => $postId, 	'TYP' => 's');
		$resFetchData				=	$mycms->sql_select($sqlFetchData);

		$postMedia 					=	unserialize($resFetchData[0]['media']);
		$postMediaSize				=	sizeof($postMedia);
		foreach ($postMedia as $key => $postMediaValue) {
			
			if($key==$postImagePos+1){
				
				$result['media']	=	$postMediaValue;

				if($key+1<$postMediaSize){

					$result['isEnd']		=	'no';
				}
				else{

					$result['isEnd']		=	'yes';
				}
			}
		}
		echo json_encode($result);

	break;

	case 'getUpdatedDataNew':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		//print_r($postDataArr); die();

		$data 			=	array();

		$sqlFetchData			=	array();
		$sqlFetchData['QUERY']	=	"SELECT * FROM "._DB_ACC_WALL."
											WHERE  `id`		=	?";

		$sqlFetchData['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $postDataArr['postId'], 'TYP' => 's');
		$resFetchData				=	$mycms->sql_select($sqlFetchData);
		//print_r($sqlFetchData);
		$rowFetchData				=	$resFetchData[0];

		$data['result']				=	'success';
		$data['postid']				=	$rowFetchData['id'];

		$getCommentCreatorDetails	=	getEmployeeDetails1($rowFetchData['employeeId']);

		$data['creatorName']		=	$getCommentCreatorDetails['name'];
		$data['creatorImage']		=	checkFileExist("../upload/userprofilepicture/",$getCommentCreatorDetails['image']);
		$data['content']			=	nl2br($rowFetchData['content']);
		//$data['media']				=	BASE_URL.'upload/post/'.$rowFetchData['media'];
		
		if($rowFetchData['media']=="yes"){
			$data['ismedia']		=	'yes';
			$mediaAll 	=	getWallMedia($rowFetchData['id']);
			$data['mediaLength']	=	sizeof($mediaAll);
			//print_r($mediaAll);
			foreach ($mediaAll as $mediaKey => $mediaValue) {
				$data['media'][$mediaKey]['url']	=	BASE_URL.'upload/post/'.$mediaValue['media'];
				$mediaDetails						=	getimagesize("../upload/post/".$mediaValue['media']);
				$mediaWidth							=	$mediaDetails[0];
				$mediaHeight						=	$mediaDetails[1];
				$percentage							=	round((($mediaHeight-$mediaWidth)/$mediaWidth)*100);
				if($mediaHeight>500){
					$data['media'][$mediaKey]['mediaHeight']	=	'500';
				}
				else {
					$data['media'][$mediaKey]['mediaHeight']	=	$mediaHeight;
				}

				if($mediaValue['mediaType']=='youtubeLink'){

					$youtubeMedia 				=	$mediaValue['media'];
					$linkCode 					=	substr($youtubeMedia, strpos($youtubeMedia, "=") + 1);

					$data['media'][$mediaKey]['embed']				=	$linkCode;
					$data['media'][$mediaKey]['mediaType']			=	$mediaValue['mediaType'];
				}
				else {

					$data['media'][$mediaKey]['embed']				=	"";
					$mediaType					=	explode("/",$mediaValue['mediaType']);
					$data['media'][$mediaKey]['mediaType']			=	$mediaType[0];
				}
			}
		}
		else {

			$data['ismedia']		=	'no';
		}
//		$data['tagFriend']			=	$rowFetchData['tagFriend'];
        if($rowFetchData['tagFriend']!=''){
            $tagFriendArr 		=	explode(",", $rowFetchData['tagFriend']);
            $tagFriendIdCount 	=	count($tagFriendArr);

            $tagFriendDetails					=	getEmployeeDetails1($tagFriendArr[0]);
            $tagFriendName 						=	$tagFriendDetails['name'];

            if($tagFriendIdCount>2){
                $otherValue 	=	"Others";
            } else {
                $otherValue 	=	"Other";
            }
            if (in_array($_SESSION['user_empid'], $tagFriendArr)) {
                $data['tag_friend']				=	'You';
                $data['tag_friend_id']			=	$_SESSION['user_empid'];
                if($tagFriendIdCount>1){
                    $data['tag_friend_other_count']	=	$tagFriendIdCount-1;
                    $data['tag_friend_other']			=	$otherValue;
                    foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
                        //if ($keyFriendArr!=0) {
                        if ($valueFriendArr!=$_SESSION['user_empid']) {
                            $tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
                            $data['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
                            $data['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
                        }
                    }
                }

            }
            else{
                $data['tag_friend']					=	$tagFriendName;
                $data['tag_friend_id']				=	$tagFriendArr[0];
                if($tagFriendIdCount>1){
                    $data['tag_friend_other_count']	=	$tagFriendIdCount-1;
                    $data['tag_friend_other']			=	$otherValue;
                    foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
                        if ($keyFriendArr!=0) {
                            $tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
                            $data['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
                            $data['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
                        }
                    }
                }
            }
        }
        else{

            $data['tag_friend']	=	"";
        }

		$data['colourApplied']		=	$rowFetchData['colourApplied'];
		$data['timeBefore']			=	calculateTime($rowFetchData['createdDate']);

		if($rowFetchData['employeeId']==$_SESSION['user_empid']){

			$data['isDelete']		=	'yes';
		}
		else {

			$data['isDelete']		=	'no';
		}


		$likeDetails				=	getCommentAndLikeCounter($postDataArr['postId']);
        //print_r($likeDetails);
		$data['yourLike']			=	$likeDetails['yourLike'];
		$data['likeCounter']		=	$likeDetails['likeCounter'];

		$data['yourComment']		=	$likeDetails['yourComment'];
		$data['commentCounter']		=	$likeDetails['commentCounter'];

		$commentDetails				=	getAllCommentPostWiseNew($postDataArr['postId']);

		$commentAndNotificationCounter				=	getCommentAndLikeCounter($postDataArr['postId']);

		$data['post']['yourLike']				=	$commentAndNotificationCounter['yourLike'];
		$data['post']['yourComment']			=	$commentAndNotificationCounter['yourComment'];
		$data['post']['commentCounter']		=	$commentAndNotificationCounter['commentCounter'];
		$data['post']['likeCounter']			=	$commentAndNotificationCounter['likeCounter'];

		foreach ($commentAndNotificationCounter['otherLike'] as $keyOtherLike => $valueOtherLike) {
			$getEmpDetailsOtherLike = getEmployeeDetails1($valueOtherLike);
			$data['post']['likeList'][$keyOtherLike]['empName'] = $getEmpDetailsOtherLike['name'];
			$data['post']['likeList'][$keyOtherLike]['empId'] = $valueOtherLike;
		}


	foreach ($commentDetails['comment'] as $keyNew => $commentValue) {
			
			$data['comment'][$keyNew]['id']					=	$commentValue['id'];
			$data['comment'][$keyNew]['body']				=	$commentValue['body'];
			$data['comment'][$keyNew]['empName']			=	$commentValue['empName'];
			$data['comment'][$keyNew]['empImg']				=	$commentValue['empImg'];
			$data['comment'][$keyNew]['deleteAccess']		=	$commentValue['deleteAccess'];
			$data['comment'][$keyNew]['editAccess']			=	$commentValue['editAccess'];
		}

		echo json_encode($data);

	break;
	
	case 'show':
	
	    $postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$offset			=	($postDataArr['offset']=='')? 0 : $postDataArr['offset'];
		$limit 			=	5;
		//echo "###".$postDataArr['wallflag'];
		$wallflag 		=	($postDataArr['wallflag']=='')? 'first' : $postDataArr['wallflag'];
		$lastrow 		=	($postDataArr['lastrow']=='')? 0 : $postDataArr['lastrow'];
		$firstrow 		=	($postDataArr['firstrow']=='')? 0 : $postDataArr['firstrow'];
		
		$empid 			=	$postDataArr['empid'];
		$postid 		=	$postDataArr['postId'];

//		echo "###".$wallflag;
//		echo "###".$empid;
//		echo "####".$postDataArr['empid'];
//		echo "####".$postDataArr['type'];
		$groupArray		=	array();
		$sqlWall		=	array();
		if ($empid=='mypost') {
			if($wallflag=='first') {
				$sqlWall['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['QUERY']	=	"SELECT *
											FROM "._DB_ACC_WALL."
											WHERE `status` 			=    ?
												AND `employeeId`	=	?
											ORDER BY `id` DESC  LIMIT $offset,$limit";
				// print_r($sqlWall);
			} else if($wallflag=='new') {
				$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."
											 WHERE `status` 		=    ?
											 AND   `employeeId` 	=	 ?
											 ORDER BY `id` DESC LIMIT 0,1";
											
				$sqlWall['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
			} else if($wallflag=='old') {
				$sqlWall['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $lastrow,						'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $firstrow,					'TYP' => 's');
				$sqlWall['QUERY']	=	"SELECT *
											FROM "._DB_ACC_WALL."
											WHERE `status` 			=   ?
												AND `employeeId`	=	?
												AND  (`id` 		>	 ?
												OR   `id`		< 	 ?)
											ORDER BY `id` DESC LIMIT $offset,$limit";
			}
		} else {
			$whereOwnWallCondition			=	"";
			if($wallflag=='first'){
				$sqlWall['PARAM'][]			=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
				if ($postDataArr['empid']!="" && $postDataArr['type']=="friend") {
					$wallListOfEmployeeId	=	$postDataArr['empid'];
					if ($postDataArr['empid']!=$_SESSION['user_empid']) {
						$whereOwnWallCondition	=	" AND (((`employeeId`	=	? AND `privacyStatus`	=	?) OR (`employeeId`	=	? AND FIND_IN_SET(?, `tagFriend`)))
														OR ((`employeeId`	=	? AND `privacyStatus`	=	?) OR (`employeeId`	=	? AND FIND_IN_SET(?, `tagFriend`))))
														AND `employeeId` IS NOT NULL";
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					} else if ($postDataArr['empid']==$_SESSION['user_empid']) {
						$whereOwnWallCondition	=	" AND ((`employeeId`	=	? AND (`privacyStatus`	=	? OR `privacyStatus`	=	?))
														OR (`employeeId` IS NULL AND FIND_IN_SET(?, `tagFriend`))
														OR (`employeeId` !=	? AND FIND_IN_SET(?, `tagFriend`)))";
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'tag',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					}
				} else {
					$wallListOfEmployeeId	=	$_SESSION['user_empid'];
					$whereOwnWallCondition	=	" AND (`employeeId`	=	?
													OR 	(`employeeId`	!=	? AND `privacyStatus`	=	?)
													OR 	FIND_IN_SET('".$wallListOfEmployeeId."',`tagFriend`))";
					$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
				}
				if($groupString!=''){
					$where 				=	" OR (`employeeId`	!=	? AND `groupId` IN (".$groupString."))";
					$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',			'DATA' => $wallListOfEmployeeId,	'TYP' => 's');
				}
				else {
					$where 	=	"";
				}
				$sqlWall['QUERY']	=	"SELECT *
											FROM "._DB_ACC_WALL."
											WHERE `status` =    ?
											".$whereOwnWallCondition."
											ORDER BY `id` DESC LIMIT $offset,$limit";


			}
			else if($wallflag=='new'){
				$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."
											 WHERE `status` 		=    ?
											 AND   `employeeId` 	=	 ?
											 ORDER BY `id` DESC LIMIT 0,1";
											
				$sqlWall['PARAM'][]	=	array('FILD' => 'status',		'DATA' => 'A',		'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',	'DATA' => $empid,	'TYP' => 's');
			}
			else if($wallflag=='old'){
				$sqlWall['PARAM'][]			=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
				if ($postDataArr['empid']!="" && $postDataArr['type']=="friend") {
					$wallListOfEmployeeId	=	$postDataArr['empid'];
					if ($postDataArr['empid']!=$_SESSION['user_empid']) {
						$whereOwnWallCondition	=	" AND (((`employeeId`	=	? AND `privacyStatus`	=	?) OR (`employeeId`	=	? AND FIND_IN_SET(?, `tagFriend`)))
														OR ((`employeeId`	=	? AND `privacyStatus`	=	?) OR (`employeeId`	=	? AND FIND_IN_SET(?, `tagFriend`))))
														AND `employeeId` IS NOT NULL";
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					} else if ($postDataArr['empid']==$_SESSION['user_empid']) {
						$whereOwnWallCondition	=	" AND ((`employeeId`	=	? AND (`privacyStatus`	=	? OR `privacyStatus`	=	?))
														OR (`employeeId` IS NULL AND FIND_IN_SET(?, `tagFriend`))
														OR (`employeeId` !=	? AND FIND_IN_SET(?, `tagFriend`)))";
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'tag',						'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
						$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					}
					/*$whereOwnWallCondition	=	" AND (`employeeId`	=	?
													OR (FIND_IN_SET('".$wallListOfEmployeeId."',`tagFriend`) AND FIND_IN_SET('".$_SESSION['user_empid']."',`tagFriend`)))
													AND `employeeId` IS NOT NULL";
					$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');*/
				} else {
					$wallListOfEmployeeId	=	$_SESSION['user_empid'];
					$whereOwnWallCondition	=	" AND (`employeeId`	=	?
													OR 	(`employeeId`	!=	? AND `privacyStatus`	=	?)
													OR 	FIND_IN_SET('".$wallListOfEmployeeId."',`tagFriend`))";
					$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',		'DATA' => $wallListOfEmployeeId,		'TYP' => 's');
					$sqlWall['PARAM'][]		=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');
				}
				/*$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'privacyStatus',	'DATA' => 'Public',						'TYP' => 's');*/

				$sqlWall['QUERY']	=	"SELECT *
											FROM "._DB_ACC_WALL."
											WHERE `status` 	=   ?
											".$whereOwnWallCondition."
											AND  (`id` 		>	 ?
											OR   `id`		< 	 ?)
											ORDER BY `id` DESC LIMIT $offset,$limit";
				/*if($groupString!=''){
					$where 				=	" OR (`employeeId`	!=	? AND `groupId` IN (".$groupString."))";
					$sqlWall['PARAM'][]	=	array('FILD' => 'employeeId',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				}
				else {
					$where 	=	"";
				}*/

				/*$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."
											 WHERE `status` 	=   ?
											 AND  (`employeeId`	=	?
											 OR   (`employeeId`	!=	? AND `privacyStatus`	=	?)
											 ".$where." 
											 OR   FIND_IN_SET(".$_SESSION['user_empid'].",`tagFriend`))
											 AND  (`id` 		>	 ?
											 OR   `id`			< 	 ?)
											 ORDER BY `id` DESC LIMIT $offset,$limit";*/
											
				//$sqlWall['PARAM'][]	=	array('FILD' => 'tagFriend',		'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $lastrow,						'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $firstrow,					'TYP' => 's');
				//print_r($sqlWall);//die();
			}
			else if($wallflag=='static'){
				$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."
											 WHERE `status` 	=    ?
											 AND   `id` 		=	 ?";
											
				$sqlWall['PARAM'][]	=	array('FILD' => 'status',		'DATA' => 'A',			'TYP' => 's');
				$sqlWall['PARAM'][]	=	array('FILD' => 'id',			'DATA' => $postid,		'TYP' => 's');
				
				//print_r($sqlWall);die();
			}
			else if($wallflag=='red'){
				$whereForRedPost		=	"";
				$sqlWall['PARAM'][]		=	array('FILD' => 'tagFriend',			'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'colourApplied',		'DATA' => 'red',						'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'status',				'DATA' => 'A',							'TYP' => 's');

				$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',			'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'colourApplied',		'DATA' => 'red',						'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'status',				'DATA' => 'A',							'TYP' => 's');

				if ($lastrow!="") {

					$whereForRedPost	=	" AND `id`	> 	?";
					$sqlWall['PARAM'][]	=	array('FILD' => 'id',				'DATA' => $lastrow,						'TYP' => 's');
				}
				$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."      
											 WHERE 
											 (FIND_IN_SET(?, `tagFriend`) AND `colourApplied`	 	= ? AND `status` =    ?)
											 OR 	(`employeeId`	=	?  AND `tagFriend` != ''
											 AND    `colourApplied`	 	= ? AND `status` =    ?)
											 ".$whereForRedPost."
											 ORDER BY `id` DESC  ";

			}
			else if($wallflag=='green'){

				$whereForGreenPost		=	"";
				$sqlWall['PARAM'][]		=	array('FILD' => 'tagFriend',			'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'colourApplied',		'DATA' => 'green',						'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'status',				'DATA' => 'A',							'TYP' => 's');

				$sqlWall['PARAM'][]		=	array('FILD' => 'employeeId',			'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'colourApplied',		'DATA' => 'green',						'TYP' => 's');
				$sqlWall['PARAM'][]		=	array('FILD' => 'status',				'DATA' => 'A',							'TYP' => 's');


				if ($lastrow!="" && $firstrow!="") {
					$whereForGreenPost	=	" AND (`id`	> 	?
												OR `id`< ?)";
					$sqlWall['PARAM'][]	=	array('FILD' => 'id',					'DATA' => $lastrow,						'TYP' => 's');
					$sqlWall['PARAM'][]	=	array('FILD' => 'id',					'DATA' => $firstrow,						'TYP' => 's');
				}
				$sqlWall['QUERY']	=	"SELECT *
											 FROM "._DB_ACC_WALL."
											 WHERE 
											 (FIND_IN_SET(?, `tagFriend`)  AND    `colourApplied`	 	= ? AND `status` =    ?)
											 OR 	(`employeeId`	=	?   AND `tagFriend` != ''
											 AND    `colourApplied`	 	= ? AND `status` =    ?)
											 ".$whereForGreenPost."
											 ORDER BY `id` DESC ";

			}
		}

		$res 			=	$mycms->sql_select($sqlWall);
//		print_r($sqlWall);

		$data 			=	array();
		if ($postDataArr['empid']!="" && $postDataArr['type']=="friend") {
			$wallListOfEmployeeId	=	$postDataArr['empid'];
			if ($postDataArr['empid']==$_SESSION['user_empid']) {
				$data['isFriendWall']	=	'true';
			} else {
				$data['isFriendWall']	=	'false';
			}
		} else {
			$wallListOfEmployeeId	=	$_SESSION['user_empid'];
			$data['isFriendWall']	=	'true';
		}
		if ($postDataArr['type']=="friend") {
			$data['mainWall']		=	'false';
		} else {
			$data['mainWall']		=	'true';
		}

		$getDashboardWallPersonDetails		=	getEmployeeDetails1($wallListOfEmployeeId);
		$data['loginEmployeeId']	=	$wallListOfEmployeeId;
		$_SESSION['empBranchId']	=	getBranctIdByEmployeeId($getDashboardWallPersonDetails['id']);
		$data['employeeName']		=	$getDashboardWallPersonDetails['name'];
		$data['employeeDOB']		=	($getDashboardWallPersonDetails['dob']!="")?date('d-m-Y', strtotime($getDashboardWallPersonDetails['dob'])):"";
		$data['employeeDOJ']		=	($getDashboardWallPersonDetails['doj']!="")?date('d-m-Y', strtotime($getDashboardWallPersonDetails['doj'])):"";
		$data['employeeDesig']		=	($getDashboardWallPersonDetails['empDesig']!="")?$getDashboardWallPersonDetails['empDesig']:"Employee";
		$data['employeeImage']		=	checkFileExist("../upload/userprofilepicture/",$getDashboardWallPersonDetails['image']);
		$data['post'] 	=	array();
		// print_r($data);
		foreach ($res as $key => $value) {
			$sqlEmp 	=	array();
			$sqlEmp['QUERY'] 	=	"SELECT * 
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id`		=	?";

			$sqlEmp['PARAM'][]	=	array('FILD' => 'id',		'DATA' => $value['employeeId'],		'TYP' => 's');

			$resEmp 			=	$mycms->sql_select($sqlEmp);

			$data['post'][$key]['postid']		=	$value['id'];
			$data['post'][$key]['postcolour']	=	$value['colourApplied'];

			if($value['groupId']!=''){

				$data['post'][$key]['groupPost']	=	'yes';
				$data['post'][$key]['groupName']	=	getGroupDetails($value['groupId']);
			}
			else {

				$data['post'][$key]['groupPost']	=	'no';
				$data['post'][$key]['groupName']	=	'';
			}

			if($value['employeeId']!=""){
				$data['post'][$key]['emp_id']		=	$value['employeeId'];
				$data['post'][$key]['emp_name']		=	$resEmp[0]['employeeName'];
				if($resEmp[0]['employeeProfilePic']!=''){
					if(file_exists("../upload/userprofilepicture/".$resEmp[0]['employeeProfilePic'])){
						$profilePicture		=	BASE_URL."upload/userprofilepicture/".$resEmp[0]['employeeProfilePic'];
					}
					else{
						$profilePicture		=	BASE_URL."images/no_img.jpg";
					}
				}
				else{
					$profilePicture		=	BASE_URL."images/no_img.jpg";
				}
			} else {
				include_once("../includes/configure.override.php");
				swapDatabaseConnect(2,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);
				$getCompanyName 	=	getCompanyName($_SESSION['user_cpanelid']);
				swapDatabaseDisconnect(2);
				$data['post'][$key]['emp_name']		=	$getCompanyName;
				$profilePicture						=	BASE_URL."images/company.png";
			}

			

			if($value['tagFriend']!=''){
				$tagFriendArr 		=	explode(",", $value['tagFriend']);
				$tagFriendIdCount 	=	count($tagFriendArr);

				$tagFriendDetails					=	getEmployeeDetails1($tagFriendArr[0]);
				$tagFriendName 						=	$tagFriendDetails['name'];

				if($tagFriendIdCount>2){
					$otherValue 	=	"Others";
				} else {
					$otherValue 	=	"Other";
				}
				if (in_array($_SESSION['user_empid'], $tagFriendArr)) {
					$data['post'][$key]['tag_friend']				=	'You';
					$data['post'][$key]['tag_friend_id']			=	$_SESSION['user_empid'];
					if($tagFriendIdCount>1){
						$data['post'][$key]['tag_friend_other_count']	=	$tagFriendIdCount-1;
						$data['post'][$key]['tag_friend_other']			=	$otherValue;
						foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
							//if ($keyFriendArr!=0) {
							if ($valueFriendArr!=$_SESSION['user_empid']) {
								$tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
								$data['post'][$key]['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
								$data['post'][$key]['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
							}
						}
					}
					
				}
				else{
					$data['post'][$key]['tag_friend']					=	$tagFriendName;
					$data['post'][$key]['tag_friend_id']				=	$tagFriendArr[0];
					if($tagFriendIdCount>1){
						$data['post'][$key]['tag_friend_other_count']	=	$tagFriendIdCount-1;
						$data['post'][$key]['tag_friend_other']			=	$otherValue;
						foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
							if ($keyFriendArr!=0) {
								$tagFriendDetailsValue	=	getEmployeeDetails1($valueFriendArr);
								$data['post'][$key]['tag_friend_list'][$keyFriendArr]['name']		=	$tagFriendDetailsValue['name'];
								$data['post'][$key]['tag_friend_list'][$keyFriendArr]['id']		    =	$valueFriendArr;
							}
						}
					}
				}
			}
			else{

				$data['post'][$key]['tag_friend']	=	"";
			}

			if($value['employeeId']==$_SESSION['user_empid']){

				$data['post'][$key]['deleteAccess']	=	'yes';
			}
			else{

				$data['post'][$key]['deleteAccess']	=	'no';
			}

			

			$data['post'][$key]['emp_pic']		=	$profilePicture;

			// if(strrchr($value['content'],"@")){

			// 	$tagfrienddetails					=	getEmployeeDetails1($value['tagFriend']);
			// 	$tagfriendname						=	$tagfrienddetails['name'];
			// 	$replaceString						=	'@'.$tagfriendname;
            //     $data['post'][$key]['content']		=	base64_encode(htmlentities($value['content'], ENT_NOQUOTES));
			// }
			// else{
			// 	$data['post'][$key]['content']		=	base64_encode(nl2br(htmlentities($value['content'], ENT_NOQUOTES)));
			// }

			$data['post'][$key]['content']		=	base64_encode(nl2br(htmlentities($value['content'], ENT_NOQUOTES)));
			
			//$data['post'][$key]['content']		=	$value['content'];
			$data['post'][$key]['posted_time']		=	calculateTime($value['createdDate']);
			$data['post'][$key]['likestatus']		=	getPostLikeSatus($_SESSION['user_empid'],$value['id']);
			if($value['media']=='yes'){
				$data['post'][$key]['ismedia']		=	'true';
				/*$mediaType 							=	explode('/', $value['mediaType']);
				if($value['mediaType']=='youtubeLink'){

					$youtubeMedia 						=	$value['media'];
					$linkCode 							=	substr($youtubeMedia, strpos($youtubeMedia, "=") + 1);
					$data['post'][$key]['media']		=	$linkCode;
					$data['post'][$key]['mediatype']	=	$value['mediaType'];
				}
				else {

					//For multiple image concept

					$checkSerialized 	=	@unserialize($value['media']);
					if ($checkSerialized!==false) {
						$mediaPhoto			=	unserialize($value['media']);
						if ($mediaType[0]=='image') {
							$length 			=	sizeof($mediaPhoto);
							if($length>5){

								$mediaPhoto	=	array_slice($mediaPhoto, 0, 5);
							}					
							foreach ($mediaPhoto as $keyMedia => $mediaPhotoValue) {
								
								$data['post'][$key]['media'][$keyMedia]['media']			=	$mediaPhotoValue;
								$data['post'][$key]['media'][$keyMedia]['mediaPosition']	=	$keyMedia;

								$mediaDetails						=	getimagesize("../upload/post/".$mediaPhotoValue);
								$mediaWidth							=	$mediaDetails[0];
								$mediaHeight						=	$mediaDetails[1];
								$percentage							=	round((($mediaHeight-$mediaWidth)/$mediaWidth)*100);
							if($mediaHeight>500){

								$data['post'][$key]['media'][$keyMedia]['mediaHeight']	=	'500';
							}
							else {

								$data['post'][$key]['media'][$keyMedia]['mediaHeight']	=	$mediaHeight;
							}
							}
							$data['post'][$key]['mediaLength']	=	$length;
						} else {
							$data['post'][$key]['media']			=	$mediaPhoto[0];
						}
						
					} else {
						$data['post'][$key]['media'][0]['media']			=	$value['media'];
						$data['post'][$key]['media'][0]['mediaPosition']	=	"0";
						$data['post'][$key]['mediaLength']					=	"1";

						$mediaDetails						=	getimagesize("../upload/post/".$value['media']);
						$mediaWidth							=	$mediaDetails[0];
						$mediaHeight						=	$mediaDetails[1];
						$percentage							=	round((($mediaHeight-$mediaWidth)/$mediaWidth)*100);
						if($mediaHeight>500){

							$data['post'][$key]['media'][0]['mediaHeight']	=	'500';
						}
						else {

							$data['post'][$key]['media'][0]['mediaHeight']	=	$mediaHeight;
						}
					}
					
					$data['post'][$key]['mediatype']	=	$mediaType[0];
					$data['post'][$key]['mediatypeall']	=	$value['mediaType'];	
				}*/

				$sqlMedia 			=	array();
				$sqlMedia['QUERY']	=	"SELECT * 
											FROM "._DB_ACC_WALL_MEDIA."
											WHERE `wallId`		=	?
											AND   `status`		=	?";

				$sqlMedia['PARAM'][]	=	array("FILD" => "wallId", "DATA" => $value['id'], 	"TYP" => "s");
				$sqlMedia['PARAM'][]	=	array("FILD" => "status", "DATA" => "A", 			"TYP" => "s");
				
				$resMedia 				=	$mycms->sql_select($sqlMedia);
				$numMedia 				=	$mycms->sql_numrows($resMedia);
				if ($numMedia==1 && $resMedia[0]['mediaType']=="youtubeLink") {
					$youtubeMedia 						=	$resMedia[0]['media'];
					$linkCode 							=	substr($youtubeMedia, strpos($youtubeMedia, "=") + 1);
					$data['post'][$key]['media']		=	$linkCode;
					$data['post'][$key]['mediatype']	=	$resMedia[0]['mediaType'];
				} else {
					if ($numMedia>5) {
						$mediaPhotoArr	=	array_slice($resMedia, 0, 5);
					} else {
						$mediaPhotoArr	=	$resMedia;
					}
					foreach ($mediaPhotoArr as $keyMedia => $valueMedia) {
						$mediaType 							=	explode('/', $valueMedia['mediaType']);
						$data['post'][$key]['media'][$keyMedia]['media']			=	$valueMedia['media'];
						$data['post'][$key]['media'][$keyMedia]['mediaPosition']	=	$keyMedia;
						$data['post'][$key]['media'][$keyMedia]['mediaType']		=	$mediaType[0];
						$data['post'][$key]['media'][$keyMedia]['mediaTypeAll']		=	$valueMedia['mediaType'];
						/*$img                =   "../upload/post/".$valueMedia['media'];
                        $getImageSize       =   getimagesize("../upload/post/".$valueMedia['media']);
                        $image              =   imagecreatefromjpeg($img);
                        $exif               =   read_exif_data($img);
                        if (!empty($exif['Orientation'])) {
                            if ($exif["Orientation"]==6) {
                                // photo needs to be rotated
                                $getImageSize       =   getimagesize("../upload/post/".$valueMedia['media']);
                                $data['post'][$key]['media'][$keyMedia]['isRotatable']	=	'yes';
                                $mediaWidth         =   $getImageSize[1];
                            	$mediaHeight        =   $getImageSize[0];
                            }
                        } else {
                            $getImageSize       =   getimagesize("../upload/post/".$valueMedia['media']);
                            $mediaWidth         =   $getImageSize[0];
                            $mediaHeight        =   $getImageSize[1];
                            $data['post'][$key]['media'][$keyMedia]['isRotatable']	=	'no';
                        }*/
                        //$data['post'][$key]['media'][$keyMedia]['media']			=	$valueMedia['media'];
						$mediaDetails						=	getimagesize("../upload/post/".$valueMedia['media']);
						$mediaWidth							=	$mediaDetails[0];
						$mediaHeight						=	$mediaDetails[1];
						$percentage							=	round((($mediaHeight-$mediaWidth)/$mediaWidth)*100);
						if ($mediaType[0]=='image') {
							if($mediaHeight>500){
								$data['post'][$key]['media'][$keyMedia]['mediaHeight']	=	'500px';
							}
							else {
								$data['post'][$key]['media'][$keyMedia]['mediaHeight']	=	(string)$mediaHeight;
							}
							if ($mediaWidth>500) {
								$data['post'][$key]['media'][$keyMedia]['mediaWidth']	=	'500px';
							} else {
								$data['post'][$key]['media'][$keyMedia]['mediaWidth']	=	(string)$mediaWidth;
							}
						} else if ($mediaType[0]=='video') {
							$data['post'][$key]['media'][$keyMedia]['videoMediaWidth']		=	"230px";
							$data['post'][$key]['media'][$keyMedia]['videoMediaHeight']		=	"220px";
						}
						
					}
					$data['post'][$key]['mediaLength']	=	$numMedia;
				}
			}
			else {
				$data['post'][$key]['ismedia']		=	'false';
			}

			$sqlCheckCommentLike 				=	array();
			$sqlCheckCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
    																WHERE  `commentId`		=	?
       															 	AND    `employeeId`		=	?
            														AND    `status`			=	?";

			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $value['employeeId'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resCheckCommentLike				=	$mycms->sql_select($sqlCheckCommentLike);
			$numCheckCommentLike				=	$mycms->sql_numrows($resCheckCommentLike);
			if($numCheckCommentLike>0){

				$result['post'][$key]['myLike']		=	'yes';
			}
			else{

				$result['post'][$key]['myLike']		=	'no';
			}



			$fetchAllComment			=	array();
			$fetchAllComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
													 WHERE `postId`	=	? 
													   AND `status`	=	?
												  ORDER BY `id` DESC
													 LIMIT 0,2";

			$fetchAllComment['PARAM'][]	=	array('FILD' => 'postId', 'DATA' => $value['id'], 		'TYP' => 's');
			$fetchAllComment['PARAM'][]	=	array('FILD' => 'status', 'DATA' => 'A', 				'TYP' => 's');
			
			$resAllComment				=	$mycms->sql_select($fetchAllComment);
			$rowAllComent 				=	$resAllComment[1];
			$numAllComment				=	$mycms->sql_numrows($resAllComment);
			if($numAllComment>0){

				$reverseArray	=	array_reverse($resAllComment);

				foreach ($reverseArray as $keyNew => $rowAllComment) {
					
					$getEmpDetails		=	getEmployeeDetails1($rowAllComment['empId']);
					$empName 			=	$getEmpDetails['name'];

	

					$filePath 		=	"../upload/userprofilepicture/";
					$empPicture 	=	checkFileExist($filePath,$getEmpDetails['image']);

					$data['post'][$key]['comments'][$keyNew]['id']				=	$rowAllComment['id'];

					if($rowAllComment['empId']==$_SESSION['user_empid']){

						$data['post'][$key]['comments'][$keyNew]['commentDeleteOption']	=	'yes';
					}
					else{

						$data['post'][$key]['comments'][$keyNew]['commentDeleteOption']	=	'no';
					}
					$data['post'][$key]['comments'][$keyNew]['empName']			=	$empName;
					$data['post'][$key]['comments'][$keyNew]['empPicture']		=	$empPicture;
					$data['post'][$key]['comments'][$keyNew]['empId']			=	$rowAllComment['empId'];
					$data['post'][$key]['comments'][$keyNew]['postId']			=	$rowAllComment['postId'];
					$data['post'][$key]['comments'][$keyNew]['commentBody']		=	$rowAllComment['commentBody'];
					$data['post'][$key]['comments'][$keyNew]['commentType']		=	$rowAllComment['type'];

					$sqlCheckCommentLike 				=	array();
					$sqlCheckCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
																	WHERE  `commentId`		=	?
																	AND    `employeeId`		=	?
																	AND    `status`			=	?";

					$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
					$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
					$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
					$resCheckCommentLike				=	$mycms->sql_select($sqlCheckCommentLike);
					$numCheckCommentLike				=	$mycms->sql_numrows($resCheckCommentLike);
					if($numCheckCommentLike>0){
						$data['post'][$key]['comments'][$keyNew]['myLike']		=	'yes';
					}
					else{
						$data['post'][$key]['comments'][$keyNew]['myLike']		=	'no';
					}

					$sqlOtherCommentLike 				=	array();
					$sqlOtherCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
																	WHERE  `commentId`		=	?
																	AND    `employeeId`		!=	?
																	AND    `status`			=	?
																	ORDER BY `id` DESC";
					$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
					$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
					$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
					$resOtherCommentLike				=	$mycms->sql_select($sqlOtherCommentLike);
					$numOtherCommentLike				=	$mycms->sql_numrows($resOtherCommentLike);
					if ($numOtherCommentLike>0) {
						foreach ($resOtherCommentLike as $keyCommentLike => $rowOtherCommentLike) {
							$data['post'][$key]['comments'][$keyNew]['commentLikePerson'][$keyCommentLike]['empId']		=	$rowOtherCommentLike['employeeId'];
							$getEmpDetailsLikeComment	=	getEmployeeDetails1($rowOtherCommentLike['employeeId']);
							$data['post'][$key]['comments'][$keyNew]['commentLikePerson'][$keyCommentLike]['empName']	=	$getEmpDetailsLikeComment['name'];
						}
					} else {
						$data['post'][$key]['comments'][$keyNew]['commentLikePerson']	=	array();
					}
					$data['post'][$key]['comments'][$keyNew]['otherLike']		=	$numOtherCommentLike;

					$commentReplyCounter 		=	getCommentReplyCommentCounter($rowAllComment['id']);

					$data['post'][$key]['comments'][$keyNew]['myComment']				=	$commentReplyCounter['myComment'];
					$data['post'][$key]['comments'][$keyNew]['otherCommentCounter']		=	$commentReplyCounter['commentCounter'];

					$sqlCommentReply				=	array();
					$sqlCommentReply['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_."
																WHERE  `commentId`		=	?
																AND    `status`			=	?
															ORDER BY   `id` DESC";

					$sqlCommentReply['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
					$sqlCommentReply['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
					$resCommentReply			=	$mycms->sql_select($sqlCommentReply);
					$numCommentReply 			=	$mycms->sql_numrows($resCommentReply);
					
					$sqlCommentReply1				=	array();
					$sqlCommentReply1['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_."
																WHERE  `commentId`		=	?
																AND    `status`			=	?
															ORDER BY   `id` DESC LIMIT 0,2";

					$sqlCommentReply1['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
					$sqlCommentReply1['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
					$resCommentReply1				=	$mycms->sql_select($sqlCommentReply1);
					$numCommentReply1 				=	$mycms->sql_numrows($resCommentReply1);
					if($numCommentReply>0){
						asort($resCommentReply1);
						foreach ($resCommentReply1 as $keyReply => $rowCommentReply) {
							
							if($keyReply<2){

								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['replyId']			=	$rowCommentReply['id'];
								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['totalCommentRely']	=	$numCommentReply;
								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['replyType']		=	$rowCommentReply['type'];

								$employeeDetails 			=	getEmployeeDetails1($rowCommentReply['employeeId']);
								$employeeProfilePic			=	$employeeDetails['image'];
								$employeeName 				=	$employeeDetails['name'];

								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['empId']			=	$rowCommentReply['employeeId'];
								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['replyerName']		=	$employeeName;
								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['replyBody']		=	$rowCommentReply['replyBody'];
								$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['replyerPic']		=	checkFileExist("../upload/userprofilepicture/",$employeeProfilePic);

								if($value['employeeId']==$_SESSION['user_empid']){

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['isDelete']		=	'yes';
								}
								else{

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['isDelete']		=	'no';
								}

								if($rowCommentReply['employeeId']==$_SESSION['user_empid'] || $value['employeeId']==$_SESSION['user_empid']){

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['deleteAccess']		=	'yes';
								}
								else{

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['deleteAccess']		=	'no';
								}
								if($rowCommentReply['employeeId']==$_SESSION['user_empid']){

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['editAccess']		=	'yes';
								}
								else{

									$data['post'][$key]['comments'][$keyNew]['commentReply'][$keyReply]['editAccess']		=	'no';
								}
							}
						}
					}
					else{

						$data['post'][$key]['comments'][$keyNew]['commentReply']		=	array();
					}
				}

				$data['post'][$key]['lastId']	=	$rowAllComent['id'];

				$fetchAllComment			=	array();
				$fetchAllComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
														 WHERE `postId`	=	? 
														   AND `status`	=	?";

				$fetchAllComment['PARAM'][]	=	array('FILD' => 'postId', 'DATA' => $value['id'], 		'TYP' => 's');
				$fetchAllComment['PARAM'][]	=	array('FILD' => 'status', 'DATA' => 'A', 				'TYP' => 's');
				
				$resAllComment				=	$mycms->sql_select($fetchAllComment);
				$numAllComment				=	$mycms->sql_numrows($resAllComment);
				if($numAllComment>2){

					$data['post'][$key]['hasComment']		=	'true';
				}
				else{

					$data['post'][$key]['hasComment']		=	'false';	
				}

				$commentAndNotificationCounter				=	getCommentAndLikeCounter($value['id']);

				$data['post'][$key]['yourLike']				=	$commentAndNotificationCounter['yourLike'];
				$data['post'][$key]['yourComment']			=	$commentAndNotificationCounter['yourComment'];
				$data['post'][$key]['commentCounter']		=	$commentAndNotificationCounter['commentCounter'];
				$data['post'][$key]['likeCounter']			=	$commentAndNotificationCounter['likeCounter'];

				foreach ($commentAndNotificationCounter['otherLike'] as $keyOtherLike => $valueOtherLike) {
					$getEmpDetailsOtherLike		=	getEmployeeDetails1($valueOtherLike);
					$data['post'][$key]['likeList'][$keyOtherLike]['empName']	=	$getEmpDetailsOtherLike['name'];
					$data['post'][$key]['likeList'][$keyOtherLike]['empId']	    =	$valueOtherLike;
				}
			}
			else{

				$data['result']						=	'success';
				$data['post'][$key]['hasComment']	=	'false';
				$data['post'][$key]['comments']		=	array();

				$commentAndNotificationCounter				=	getCommentAndLikeCounter($value['id']);
				$data['post'][$key]['yourLike']				=	$commentAndNotificationCounter['yourLike'];
				$data['post'][$key]['likeCounter']			=	$commentAndNotificationCounter['likeCounter'];

				foreach ($commentAndNotificationCounter['otherLike'] as $keyOtherLike => $valueOtherLike) {
					$getEmpDetailsOtherLike		=	getEmployeeDetails1($valueOtherLike);
					$data['post'][$key]['likeList'][$keyOtherLike]['empName']	=	$getEmpDetailsOtherLike['name'];
					$data['post'][$key]['likeList'][$keyOtherLike]['empId']	    =	$valueOtherLike;
				}
			}

		}


		if($wallflag!='old'){
			$data['lastrow']				=	$res[0]['id'];
		}
		else {
			$data['lastrow']				=	$lastrow;
		}

		if($wallflag!='new'){
			$data['firstrow']			=	$res[sizeof($res)-1]['id'];
		}
		else {
			$data['firstrow']			=	$firstrow;
		}
//		 echo '<pre>';
//		 print_r($data);
		echo json_encode($data);
		
		break;

	case 'loadAllComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);


		$result						=	array();

		$sqlPost 					=	array();
		$sqlPost['QUERY']			=	"SELECT * FROM "._DB_ACC_WALL."
												WHERE  `id`		=	?
												AND    `status`	=	?";

		$sqlPost['PARAM'][]			=	array('FILD' => 'id', 		'DATA' => $postDataArr['postId'], 		'TYP' => 's');
		$sqlPost['PARAM'][]			=	array('FILD' => 'status', 	'DATA' => 'A', 							'TYP' => 's');

		$resPost 					=	$mycms->sql_select($sqlPost);
		$rowPost 					=	$resPost[0];

		if($rowPost['employeeId']==$_SESSION['user_empid']){

			$result['isDelete']		=	'yes';
		}
		else {

			$result['isDelete']		=	'no';
		}

		$fetchAllComment			=	array();
		$fetchAllComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
												 WHERE `postId`	=	? 
												   AND `status`	=	?
											  ORDER BY `id`";

		$fetchAllComment['PARAM'][]	=	array('FILD' => 'postId', 	'DATA' => $postDataArr['postId'], 		'TYP' => 's');
		$fetchAllComment['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 							'TYP' => 's');
		$resAllComment				=	$mycms->sql_select($fetchAllComment);
		$numAllComment 				=	$mycms->sql_numrows($resAllComment);
		//print_r($resAllComment);
		$newAllComment				=	array_slice($resAllComment, 0,($numAllComment-2));
		//print_r($newAllComment);
		foreach ($newAllComment as $key => $rowAllComment) {
				//print_r($rowAllComment);
			$getEmpDetails		=	getEmployeeDetails1($rowAllComment['empId']);

			//print_r($getEmpDetails);

			$empName 			=	$getEmpDetails['name'];

            if($getEmpDetails['image']!=''){

                $empPicture			=	checkFileExist("../upload/userprofilepicture/",$getEmpDetails['image']);

            }
			else{

				$empPicture			=	BASE_URL."images/no_img.jpg";
			}
			
			$result['result']							=	'success';
			$result['comments'][$key]['id']				=	$rowAllComment['id'];
			$result['comments'][$key]['empName']		=	$empName;
			$result['comments'][$key]['empPicture']		=	$empPicture;
			$result['comments'][$key]['empId']			=	$rowAllComment['empId'];
			if($rowAllComment['empId']==$_SESSION['user_empid']){

				$result['comments'][$key]['deleteAccess']		=	'yes';
			}
			else {

				$result['comments'][$key]['deleteAccess']		=	'no';
			}
			$result['comments'][$key]['postId']			=	$rowAllComment['postId'];
			$result['comments'][$key]['type']			=	$rowAllComment['type'];
			$result['comments'][$key]['commentBody']	=	$rowAllComment['commentBody'];

			$sqlCheckCommentLike 				=	array();
			$sqlCheckCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
															WHERE  `commentId`		=	?
															AND    `employeeId`		=	?
															AND    `status`			=	?";

			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resCheckCommentLike				=	$mycms->sql_select($sqlCheckCommentLike);
			$numCheckCommentLike				=	$mycms->sql_numrows($resCheckCommentLike);
			if($numCheckCommentLike>0){

				$result['comments'][$key]['myLike']		=	'yes';
			}
			else{

				$result['comments'][$key]['myLike']		=	'no';
			}

			$sqlOtherCommentLike 				=	array();
			$sqlOtherCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
															WHERE  `commentId`		=	?
															AND    `employeeId`		!=	?
															AND    `status`			=	?";

			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resOtherCommentLike				=	$mycms->sql_select($sqlOtherCommentLike);
			$numOtherCommentLike				=	$mycms->sql_numrows($resOtherCommentLike);

			$result['comments'][$key]['otherLike']		=	$numOtherCommentLike;

			$likeDetails				=	getCommentAndLikeCounter($rowAllComment['id']);
			//print_r($likeDetails);
			$result['comments'][$key]['yourLike']			=	$likeDetails['yourLike'];
			$result['comments'][$key]['likeCounter']		=	$likeDetails['likeCounter'];

			$result['comments'][$key]['yourComment']		=	$likeDetails['yourComment'];
			$result['comments'][$key]['commentCounter']		=	$likeDetails['commentCounter'];

			//************************************************ For Comment Reply Result *******************************************************\\

			$sqlCommentReply				=	array();
			$sqlCommentReply['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_."
														WHERE  `commentId`		=	?
														AND    `status`			=	?
													ORDER BY   `id` DESC";

			$sqlCommentReply['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlCommentReply['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resCommentReply			=	$mycms->sql_select($sqlCommentReply);
			$numCommentReply 			=	$mycms->sql_numrows($resCommentReply);
			if($numCommentReply>0){
				$result['comments'][$key]['commentReplyNumber']		=	$numCommentReply;
				
				$commentReplyCounter 								=	getCommentReplyCommentCounter($rowAllComment['id']);

				$result['comments'][$key]['myComment']				=	$commentReplyCounter['myComment'];
				$result['comments'][$key]['otherCommentCounter']	=	$commentReplyCounter['commentCounter'];		
				foreach ($resCommentReply as $keyReply => $rowCommentReply) {
					
					if($keyReply<2){

						$result['comments'][$key]['commentReply'][$keyReply]['replyId']				=	$rowCommentReply['id'];
						$result['comments'][$key]['commentReply'][$keyReply]['totalCommentRely']	=	$numCommentReply;

						$employeeDetails 			=	getEmployeeDetails1($rowCommentReply['employeeId']);

						if($employeeDetails['image']!=''){

							$employeeProfilePic			=	checkFileExist("../upload/userprofilepicture/",$getEmpDetails['image']);
						}
						else{

							$employeeProfilePic			=	BASE_URL."images/no_img.jpg";
						}
						$employeeProfilePic			=	$employeeDetails['image'];
						$employeeName 				=	$employeeDetails['name'];

						$result['comments'][$key]['commentReply'][$keyReply]['empId']			=	$rowCommentReply['employeeId'];
						$result['comments'][$key]['commentReply'][$keyReply]['replyerName']		=	$employeeName;
						$result['comments'][$key]['commentReply'][$keyReply]['replyBody']		=	$rowCommentReply['replyBody'];
						$result['comments'][$key]['commentReply'][$keyReply]['replyerPic']		=	checkFileExist("../upload/userprofilepicture/",$getEmpDetails['image']);
						if($rowCommentReply['employeeId']==$_SESSION['user_empid'] || $rowAllComment['empId']==$_SESSION['user_empid']){

							$result['comments'][$key]['commentReply'][$keyReply]['deleteAccess']	=	'yes';
						}
						else{

							$result['comments'][$key]['commentReply'][$keyReply]['deleteAccess']	=	'no';
						}
						if($rowCommentReply['employeeId']==$_SESSION['user_empid']){

							$result['comments'][$key]['commentReply'][$keyReply]['editAccess']		=	'yes';
						}
						else{

							$result['comments'][$key]['commentReply'][$keyReply]['editAccess']		=	'no';
						}

						$result['div']	=	'1';
					}
				}
			}
			else{

				$result['comments'][$key]['commentReply']		=	array();
			}
			//**************************************************************************************************************************************//
		}
		echo json_encode($result);

	break;

	case 'loadAllDetailsComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);


		$result						=	array();

		$sqlPost 					=	array();
		$sqlPost['QUERY']			=	"SELECT * FROM "._DB_ACC_WALL."
												WHERE  `id`		=	?
												AND    `status`	=	?";

		$sqlPost['PARAM'][]			=	array('FILD' => 'id', 		'DATA' => $postDataArr['postId'], 		'TYP' => 's');
		$sqlPost['PARAM'][]			=	array('FILD' => 'status', 	'DATA' => 'A', 							'TYP' => 's');

		$resPost 					=	$mycms->sql_select($sqlPost);
		$rowPost 					=	$resPost[0];

		if($rowPost['employeeId']==$_SESSION['user_empid']){

			$result['isDelete']		=	'yes';
		}
		else {

			$result['isDelete']		=	'no';
		}

		$fetchAllComment			=	array();
		$fetchAllComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
												 WHERE `postId`	=	? 
												   AND `status`	=	?
											  ORDER BY `id`";

		$fetchAllComment['PARAM'][]	=	array('FILD' => 'postId', 	'DATA' => $postDataArr['postId'], 		'TYP' => 's');
		$fetchAllComment['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 							'TYP' => 's');
		$resAllComment				=	$mycms->sql_select($fetchAllComment);
		$numAllComment 				=	$mycms->sql_numrows($resAllComment);
		//print_r($resAllComment);
		$newAllComment				=	array_slice($resAllComment, 0,($numAllComment-2));
		//print_r($newAllComment);
		foreach ($resAllComment as $key => $rowAllComment) {
				//print_r($rowAllComment);
			$getEmpDetails		=	getEmployeeDetails1($rowAllComment['empId']);

			//print_r($getEmpDetails);

			$empName 			=	$getEmpDetails['name'];

            if($getEmpDetails['image']!=''){

                $empPicture			=	checkFileExist("../upload/userprofilepicture/",$getEmpDetails['image']);

            }
			else{

				$empPicture			=	BASE_URL."images/no_img.jpg";
			}
			
			$result['result']							=	'success';
			$result['comments'][$key]['id']				=	$rowAllComment['id'];
			$result['comments'][$key]['empName']		=	$empName;
			$result['comments'][$key]['empPicture']		=	$empPicture;
			$result['comments'][$key]['empId']			=	$rowAllComment['empId'];
			if($rowAllComment['empId']==$_SESSION['user_empid']){

				$result['comments'][$key]['deleteAccess']		=	'yes';
			}
			else {

				$result['comments'][$key]['deleteAccess']		=	'no';
			}
			$result['comments'][$key]['postId']			=	$rowAllComment['postId'];
			$result['comments'][$key]['type']			=	$rowAllComment['type'];
			$result['comments'][$key]['commentBody']	=	$rowAllComment['commentBody'];

			$sqlCheckCommentLike 				=	array();
			$sqlCheckCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
															WHERE  `commentId`		=	?
															AND    `employeeId`		=	?
															AND    `status`			=	?";

			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
			$sqlCheckCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resCheckCommentLike				=	$mycms->sql_select($sqlCheckCommentLike);
			$numCheckCommentLike				=	$mycms->sql_numrows($resCheckCommentLike);
			if($numCheckCommentLike>0){

				$result['comments'][$key]['myLike']		=	'yes';
			}
			else{

				$result['comments'][$key]['myLike']		=	'no';
			}

			$sqlOtherCommentLike 				=	array();
			$sqlOtherCommentLike['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_LIKE_."
															WHERE  `commentId`		=	?
															AND    `employeeId`		!=	?
															AND    `status`			=	?";

			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
			$sqlOtherCommentLike['PARAM'][]		=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resOtherCommentLike				=	$mycms->sql_select($sqlOtherCommentLike);
			$numOtherCommentLike				=	$mycms->sql_numrows($resOtherCommentLike);

			$result['comments'][$key]['otherLike']		=	$numOtherCommentLike;

			$likeDetails				=	getCommentAndLikeCounter($rowAllComment['id']);
			//print_r($likeDetails);
			$result['comments'][$key]['yourLike']			=	$likeDetails['yourLike'];
			$result['comments'][$key]['likeCounter']		=	$likeDetails['likeCounter'];

			$result['comments'][$key]['yourComment']		=	$likeDetails['yourComment'];
			$result['comments'][$key]['commentCounter']		=	$likeDetails['commentCounter'];

			//************************************************ For Comment Reply Result *******************************************************\\

			$sqlCommentReply				=	array();
			$sqlCommentReply['QUERY']		=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_."
														WHERE  `commentId`		=	?
														AND    `status`			=	?
													ORDER BY   `id` DESC";

			$sqlCommentReply['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $rowAllComment['id'], 		'TYP' => 's');
			$sqlCommentReply['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
			$resCommentReply			=	$mycms->sql_select($sqlCommentReply);
			$numCommentReply 			=	$mycms->sql_numrows($resCommentReply);
			if($numCommentReply>0){
				$result['comments'][$key]['commentReplyNumber']		=	$numCommentReply;
				
				$commentReplyCounter 								=	getCommentReplyCommentCounter($rowAllComment['id']);

				$result['comments'][$key]['myComment']				=	$commentReplyCounter['myComment'];
				$result['comments'][$key]['otherCommentCounter']	=	$commentReplyCounter['commentCounter'];		
				foreach ($resCommentReply as $keyReply => $rowCommentReply) {
					
					if($keyReply<2){

						$result['comments'][$key]['commentReply'][$keyReply]['replyId']				=	$rowCommentReply['id'];
						$result['comments'][$key]['commentReply'][$keyReply]['totalCommentRely']	=	$numCommentReply;

						$employeeDetails 			=	getEmployeeDetails1($rowCommentReply['employeeId']);
						$employeeProfilePic			=	$employeeDetails['image'];
						if($employeeDetails['image']!=''){

							$employeeProfilePic			=	checkFileExist("../upload/userprofilepicture/",$employeeDetails['image']);
						}
						else{

							$employeeProfilePic			=	BASE_URL."images/no_img.jpg";
						}
						
						$employeeName 				=	$employeeDetails['name'];

						$result['comments'][$key]['commentReply'][$keyReply]['empId']			=	$rowCommentReply['employeeId'];
						$result['comments'][$key]['commentReply'][$keyReply]['replyerName']		=	$employeeName;
						$result['comments'][$key]['commentReply'][$keyReply]['replyBody']		=	$rowCommentReply['replyBody'];
						$result['comments'][$key]['commentReply'][$keyReply]['replyerPic']		=	$employeeProfilePic;
						
						
						if($rowCommentReply['employeeId']==$_SESSION['user_empid'] || $rowAllComment['empId']==$_SESSION['user_empid']){

							$result['comments'][$key]['commentReply'][$keyReply]['deleteAccess']	=	'yes';
						}
						else{

							$result['comments'][$key]['commentReply'][$keyReply]['deleteAccess']	=	'no';
						}
						if($rowCommentReply['employeeId']==$_SESSION['user_empid']){

							$result['comments'][$key]['commentReply'][$keyReply]['editAccess']		=	'yes';
						}
						else{

							$result['comments'][$key]['commentReply'][$keyReply]['editAccess']		=	'no';
						}

						$result['div']	=	'1';
					}
				}
			}
			else{

				$result['comments'][$key]['commentReply']		=	array();
			}
			//**************************************************************************************************************************************//
		}
		echo json_encode($result);

	break;

	case 'like':
		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$empid 			=	$postDataArr['empidvalue'];
		$postid 		=	$postDataArr['postid'];
		$sqlLikeCheck				=	array();
		$sqlLikeCheck['QUERY']		=	"SELECT `postId`
											FROM "._DB_ACC_WALL_LIKE_."
											WHERE `empId`	=	?
												AND `postId`	=	?
												AND `status`	=	?";
		$sqlLikeCheck['PARAM'][]	=	array('FILD' => 'empId', 	'DATA' => $empid, 		'TYP' => 's');
		$sqlLikeCheck['PARAM'][]	=	array('FILD' => 'postId', 	'DATA' => $postid, 		'TYP' => 's');
		$sqlLikeCheck['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 			'TYP' => 's');
		$resLikeCheck				=	$mycms->sql_select($sqlLikeCheck);
		$numLikeCheck				=	$mycms->sql_numrows($resLikeCheck);
		if ($numLikeCheck==0) {
			$sql 			=	array();
			$sql['QUERY'] 	=	"INSERT INTO "._DB_ACC_WALL_LIKE_."
										SET 
											`empId`				=	?,
											`postId`			=	?,
											`createdDate`		=	?,
											`createdSession`	=	?,
											`createdIp`			=	?";
			$sql['PARAM'][]	=	array('FILD' => 'empId',			'DATA' => $empid,							'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'postId',			'DATA' => $postid,							'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'createdDate',		'DATA' => date('Y-m-d'),					'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'createdSession',	'DATA' => session_id(),						'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'createdIp',		'DATA' => $_SERVER['REMOTE_ADDR'],			'TYP' => 's');
			$res 			=	$mycms->sql_insert($sql);
			$result 			=	array();
			if($res){
				$result['result']	=	'success';
				$getPostCreator					=	array();
				$getPostCreator['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
														WHERE  `id`		=	?
														AND	   `status`	=	?";

				$getPostCreator['PARAM'][]		=	array('FILD' => 'id', 		'DATA' => $postid, 			'TYP' => 's');
				$getPostCreator['PARAM'][]		=	array('FILD' => 'status', 	'DATA' => 'A', 				'TYP' => 's');
				$resPostCreator 				=	$mycms->sql_select($getPostCreator);
				$rowPostCreator					=	$resPostCreator[0];
				$employeeId						=	$rowPostCreator['employeeId'];
				if($empid!=$employeeId){
					$getNotification		=	notificationDetails('15');
					$notificationSubject	=	$getNotification['subject'];
					$notificationBody		=	$getNotification['body'];
					$getEmpDetails 			=	getEmployeeDetails1($empid);
					$getEmpName 			=	$getEmpDetails['name'];
					$dataArray				=	array('{name}');
					$replaceDataArray		=	array(ucwords($getEmpName));
					$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);
					send_notification($employeeId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Post Like", $postid);
					$device_id 				=	$_SERVER['REMOTE_ADDR'];
					/*$fcm_token 				= 	get_fcm_id($empid);
					foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
						//send_fcm_notification($_SESSION['user_empid'], $empid, $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Post Like", "Post Like", $empid);
					}*/
				}
			}
			else
			{
				$result['result']	=	'failed';
			}
		} else {
			$result['result']	=	'failed';
		}
		echo json_encode($result);
		
		break;

	case 'unlike':
		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		//print_r($postDataArr);
		$empid 			=	$postDataArr['empidvalue'];
		$postid 		=	$postDataArr['postid'];

		$sql 			=	array();
		$sql['QUERY'] 	=	"UPDATE "._DB_ACC_WALL_LIKE_."
								SET 
									`status`				=	?
								WHERE `empId`				=	?
								AND   `postId`				=	?";

		$sql['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'D',					'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'empId',			'DATA' => $empid,				'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'postId',			'DATA' => $postid,				'TYP' => 's');
		

		$res 			=	$mycms->sql_update($sql);

		$result 			=	array();
		if($res){

			$result['result']	=	'success';
		}
		echo json_encode($result);
		
		break;

	case 'fetchbirthday_old':

		$todayString 			=	date('md');
		
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$friendIdStr	=	implode(",",$friendList);
		
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										ORDER BY `employeeDob` ASC";

		
		//print_r($sqlProfile);
		$resProfile				=	$mycms->sql_select($sqlProfile);
		//print_r($resProfile);
		$data = array();

		$birthDayArr 		=	array();

		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile["employeeDob"]));

			if($dobString>=$todayString){
				$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
				$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
				$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
				$profilePicPath 			=	"../upload/userprofilepicture/";
				$birthDayArr[$dobString]['employeeProfilePic']	=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
				$birthDayArr[$dobString]['membertype']				=	'Colleague';
			}
		}

		$birthDayArrCount  	=	count($birthDayArr);
		if($birthDayArrCount<3){
			foreach ($resProfile as $key => $rowProfile) {
				$dobString 		=	date('md',strtotime($rowProfile["employeeDob"]));
				if(!in_array($dobString, $birthDayArr)){
					$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
					$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
					$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
					$profilePicPath 			=	"../upload/userprofilepicture/";
					$birthDayArr[$dobString]['employeeProfilePic']		=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
					$birthDayArr[$dobString]['membertype']				=	'Colleague';
				}
			}
		}

		
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);

		require_once("../includes/configure.override.php");
		
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";

		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
					
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);

		swapDatabaseDisconnect(4);

		

		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily[dob]));
			$todayString 		=	date('md');

			if($dobFMString>=$todayString){
				$birthDayArr[$dobFMString]['empid']					=	$rowProfilefamily['id'];
				$birthDayArr[$dobFMString]['employeeDob']			=	$rowProfilefamily['dob'];
				$birthDayArr[$dobFMString]['employeeName']			=	$rowProfilefamily['name'];
				$birthDayArr[$dobFMString]['membertype']			=	$rowProfilefamily['member_type'];
				$familyProfilePicPath 			=	"../upload/family/";
				//$birthDayArr[$dobFMString]['employeeProfilePic']	=	$rowProfilefamily['family_image'];
				$birthDayArr[$dobFMString]['employeeProfilePic']	=	checkFileExist($familyProfilePicPath,$rowProfilefamily['family_image']);
			}
		}
		ksort($birthDayArr);
		//print_r($birthDayArr);
		//$newBirthArr 	=	array();
		$newDataArr 	=	array();
		$a=0;

		foreach ($birthDayArr as $keyBV => $birthDayValue) {
			if($keyBV>=$todayString){
				$newDataArr[$a]['key']					=	$keyBV;
				$newDataArr[$a]['empid']				=	$birthDayValue['empid'];
				$newDataArr[$a]['employeeDob']			=	($keyBV==$todayString)?"Today":date('d F',strtotime($birthDayValue['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$birthDayValue['employeeName'];
				$newDataArr[$a]['employeeProfilePic']	=	$birthDayValue['employeeProfilePic'];
				/*if($birthDayValue['employeeProfilePic']!=''){
					if(file_exists('../upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'])){
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'];
					}
					else {
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
					}
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}*/
				
				$newDataArr[$a]['membertype']			=	$birthDayValue['membertype'];
				$wishExist 		=	getWishExist($_SESSION['user_empid'],$birthDayValue['empid'],"birthday");
				if ($birthDayValue['membertype']=='Colleague' && $keyBV==$todayString && $wishExist==false) {
					$newDataArr[$a]['isWishbtn']			=	"yes";
				} else {
					$newDataArr[$a]['isWishbtn']			=	"no";
				}
				$a++;

				//unset($birthDayArr[$keyBV]);
			}
		}
		

		//print_r(ksort($birthDayArr));

		
		

		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']					=	$keyBDARR;
			$newDataArr[$a]['empid']				=	$valueBDARR['empid'];
			$newDataArr[$a]['employeeDob']			=	($keyBDARR==$todayString)?"Today":date('d F',strtotime($valueBDARR['employeeDob']));
			$newDataArr[$a]['employeeName']			=	$valueBDARR['employeeName'];
			$newDataArr[$a]['employeeProfilePic']	=	$valueBDARR['employeeProfilePic'];
			/*if($valueBDARR['employeeProfilePic']!=''){
				if(file_exists('../upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'])){
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'];
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
			}
			else {
				$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
			}*/
			
			$newDataArr[$a]['membertype']			=	$valueBDARR['membertype'];
			$wishExist 		=	getWishExist($_SESSION['user_empid'],$valueBDARR['empid'],"birthday");
			if ($valueBDARR['membertype']=='Colleague' && $keyBDARR==$todayString && $wishExist==false) {
				$newDataArr[$a]['isWishbtn']			=	"yes";
			} else {
				$newDataArr[$a]['isWishbtn']			=	"no";
			}
			$a++;
		}
		//$sizeOfArray			=	sizeof($newDataArr);
		//$newDataArr['count']	=	$sizeOfArray;
		$newDataArrNew 			=	array_slice($newDataArr, 0,3);
		
		//print_r($birthDayArr);

		$employeeDetailsOwn 	=	getEmployeeDetails1($_SESSION['user_empid']);

		$sendResponse 				=	array();
		$sendResponse['itmes']		=	$newDataArrNew;
		$sendResponse['length']		=	count($newDataArr);
		$sendResponse['mybirthday']	=	(date("m-d",strtotime($employeeDetailsOwn['dob']))==date("m-d"))?"yes":"no";

		echo json_encode($sendResponse);
	break;

	/*case 'fetchbirthday':

		$todayString 			=	date('md');
		
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$friendIdStr	=	implode(",",$friendList);
		
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										ORDER BY `employeeDob` ASC";

		
		//print_r($sqlProfile);
		$resProfile				=	$mycms->sql_select($sqlProfile);
		//print_r($resProfile);
		$data = array();

		$birthDayArr 		=	array();

		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));

			if($dobString>$todayString){
				$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
				$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
				$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
				$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
				$birthDayArr[$dobString]['membertype']				=	'Colleague';
			}
		}

		$birthDayArrCount  	=	count($birthDayArr);
		if($birthDayArrCount<3){
			foreach ($resProfile as $key => $rowProfile) {
				$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));
				if(!in_array($dobString, $birthDayArr)){
					$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
					$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
					$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
					$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
					$birthDayArr[$dobString]['membertype']				=	'Colleague';
				}
			}
		}

		
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);

		require_once("../includes/configure.override.php");
		
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";

		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
					
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);

		swapDatabaseDisconnect(4);

		

		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily[dob]));
			$todayString 		=	date('md');

			if($dobFMString>$todayString){
				$birthDayArr[$dobFMString]['empid']					=	$rowProfilefamily['id'];
				$birthDayArr[$dobFMString]['employeeDob']			=	$rowProfilefamily['dob'];
				$birthDayArr[$dobFMString]['employeeName']			=	$rowProfilefamily['name'];
				$birthDayArr[$dobFMString]['membertype']			=	$rowProfilefamily['member_type'];
				$birthDayArr[$dobFMString]['employeeProfilePic']	=	$rowProfilefamily['family_image'];
			}
		}
		ksort($birthDayArr);
		//print_r($birthDayArr);
		//$newBirthArr 	=	array();
		$newDataArr 	=	array();
		$a=0;

		foreach ($birthDayArr as $keyBV => $birthDayValue) {
			if($keyBV>$todayString){
				$newDataArr[$a]['key']					=	$keyBV;
				$newDataArr[$a]['empid']				=	$birthDayValue['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($birthDayValue['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$birthDayValue['employeeName'];
				if($birthDayValue['employeeProfilePic']!=''){
					if(file_exists('../upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'])){
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'];
					}
					else {
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
					}
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
				
				$newDataArr[$a]['membertype']			=	$birthDayValue['membertype'];
				$a++;

				//unset($birthDayArr[$keyBV]);
			}
		}
		

		//print_r(ksort($birthDayArr));

		
		

		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']					=	$keyBDARR;
			$newDataArr[$a]['empid']				=	$valueBDARR['empid'];
			$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDARR['employeeDob']));
			$newDataArr[$a]['employeeName']			=	$valueBDARR['employeeName'];
			if($valueBDARR['employeeProfilePic']!=''){
				if(file_exists('../upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'])){
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'];
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
			}
			else {
				$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
			}
			
			$newDataArr[$a]['membertype']			=	$valueBDARR['membertype'];
			$a++;
		}
		$newDataArrNew 			=	array_slice($newDataArr, 0,3);

		$sendResponse 			=	array();
		$sendResponse['itmes']	=	$newDataArrNew;
		$sendResponse['length']	=	count($newDataArr);

		echo json_encode($sendResponse);
	break;*/

	case 'fetchbirthday':
		$todayString 			=	date('md');
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){
				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){
					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){
				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){
					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);
		$friendIdStr	=	implode(",",$friendList);
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										AND (`employeeCurrentStatus` = '' OR `employeeCurrentStatus` IS NULL OR `employeeCurrentStatus` = 'Active' OR `employeeCurrentStatus` = 'joining' OR `employeeCurrentStatus` = 'offerletter')
										
										ORDER BY `employeeDob` ASC";
//AND `employeeCurrentStatus` = ?
										
		//$sqlProfile['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 	'TYP' => 's');								
		// $sqlProfile['PARAM'][]	=	array('FILD' => 'employeeCurrentStatus', 		'DATA' => NULL, 	'TYP' => 's');								

		$resProfile				=	$mycms->sql_select($sqlProfile);
		$data 					= 	array();
		$birthDayArr 			=	array();
		$uniqueKey				=	0;
		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile['employeeDob']));
			$birthDayArr[$dobString][$key]['empid']					=	$rowProfile['id'];
			$birthDayArr[$dobString][$key]['employeeDob']			=	$rowProfile['employeeDob'];
			$birthDayArr[$dobString][$key]['employeeName']			=	$rowProfile['employeeName'];
			$profilePicPath 			=	"../upload/userprofilepicture/";
			$birthDayArr[$dobString][$key]['employeeProfilePic']	=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
			$birthDayArr[$dobString][$key]['membertype']			=	'Colleague';
			$birthDayArr[$dobString][$key]['uniqueKey']				=	$uniqueKey;
			$uniqueKey++;
		}
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);
		require_once("../includes/configure.override.php");
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);
		swapDatabaseDisconnect(4);
		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily['dob']));
			$todayString 		=	date('md');
			$birthDayArr[$dobFMString][$keyFM]['empid']					=	$rowProfilefamily['id'];
			$birthDayArr[$dobFMString][$keyFM]['employeeDob']			=	($rowProfilefamily['dob']!="")?date('Y-m-d', strtotime($rowProfilefamily['dob'])):'';
			$birthDayArr[$dobFMString][$keyFM]['employeeName']			=	$rowProfilefamily['name'];
			$birthDayArr[$dobFMString][$keyFM]['membertype']			=	$rowProfilefamily['member_type'];
			$familyProfilePicPath 			=	"../upload/family/";
			$birthDayArr[$dobFMString][$keyFM]['employeeProfilePic']	=	checkFileExist($familyProfilePicPath,$rowProfilefamily['family_image']);
			$birthDayArr[$dobFMString][$keyFM]['uniqueKey']				=	$uniqueKey;
			$uniqueKey++;
		}
		//krsort($birthDayArr);
		ksort($birthDayArr);
		$a	=	0;
		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			foreach ($valueBDARR as $keySub => $valueBDArrNew) {
				$newDataArr[$a]['key']					=	$keyBDARR;
				$newDataArr[$a]['empid']				=	$valueBDArrNew['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDArrNew['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$valueBDArrNew['employeeName'];
				$newDataArr[$a]['employeeProfilePic']	=	$valueBDArrNew['employeeProfilePic'];
				
				$newDataArr[$a]['membertype']			=	$valueBDArrNew['membertype'];
				$wishExist 		=	getWishExist($_SESSION['user_empid'],$valueBDArrNew['empid'],"birthday");
				if ($valueBDArrNew['membertype']=='Colleague' && $keyBDARR==$todayString && $wishExist==false) {
					$newDataArr[$a]['isWishbtn']			=	"yes";
				} else {
					$newDataArr[$a]['isWishbtn']			=	"no";
				}
				$a++;
			}
			//print_r($valueBDARR);
		}

		$newDOBarray = array();
		$oldDOBArray = array();
		foreach ($newDataArr as $key => $dobArry) {
			$thisYearDOB= 	$dobArry['employeeDob'].' '.date(Y);
			$date 		=	date_create($thisYearDOB);
			$thisYearDOBdate = date_format($date,"Y-m-d");
			$today = date("Y-m-d");

			if($thisYearDOBdate < $today){
				array_push($oldDOBArray,$dobArry);
			} else {
				array_push($newDOBarray,$dobArry);
			}

		}

		$newDataArr = array_merge($newDOBarray,$oldDOBArray);
		$newDataArrNew 				=	array_slice($newDataArr, 0,3);
		$employeeDetailsOwn 		=	getEmployeeDetails1($_SESSION['user_empid']);
		$sendResponse 				=	array();
		$sendResponse['itmes']		=	$newDataArrNew;
		$sendResponse['length']		=	count($newDataArr);
		$sendResponse['mybirthday']	=	(date("m-d",strtotime($employeeDetailsOwn['dob']))==date("m-d"))?"yes":"no";
		echo json_encode($sendResponse);
	break;

	case 'viewAllUpcommingBirthdayList':
		$todayString 			=	date('md');
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){
				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){
					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){
				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){
					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);
		$friendIdStr	=	implode(",",$friendList);
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										AND (`employeeCurrentStatus` = '' OR `employeeCurrentStatus` IS NULL OR `employeeCurrentStatus` = 'Active' OR `employeeCurrentStatus` = 'joining' OR `employeeCurrentStatus` = 'offerletter')
										ORDER BY `employeeDob` ASC";
		$resProfile				=	$mycms->sql_select($sqlProfile);
		$data 					= 	array();
		$birthDayArr 			=	array();
		$uniqueKey				=	0;
		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile['employeeDob']));
			$birthDayArr[$dobString][$key]['empid']					=	$rowProfile['id'];
			$birthDayArr[$dobString][$key]['employeeDob']			=	$rowProfile['employeeDob'];
			$birthDayArr[$dobString][$key]['employeeName']			=	$rowProfile['employeeName'];
			$profilePicPath 			=	"../upload/userprofilepicture/";
			$birthDayArr[$dobString][$key]['employeeProfilePic']	=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
			$birthDayArr[$dobString][$key]['membertype']			=	'Colleague';
			$birthDayArr[$dobString][$key]['uniqueKey']				=	$uniqueKey;
			$uniqueKey++;
		}
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);
		require_once("../includes/configure.override.php");
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);
		swapDatabaseDisconnect(4);
		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily['dob']));
			$todayString 		=	date('md');
			$birthDayArr[$dobFMString][$keyFM]['empid']					=	$rowProfilefamily['id'];
			$birthDayArr[$dobFMString][$keyFM]['employeeDob']			=	($rowProfilefamily['dob']!="")?date('Y-m-d', strtotime($rowProfilefamily['dob'])):'';
			$birthDayArr[$dobFMString][$keyFM]['employeeName']			=	$rowProfilefamily['name'];
			$birthDayArr[$dobFMString][$keyFM]['membertype']			=	$rowProfilefamily['member_type'];
			$familyProfilePicPath 			=	"../upload/family/";
			$birthDayArr[$dobFMString][$keyFM]['employeeProfilePic']	=	checkFileExist($familyProfilePicPath,$rowProfilefamily['family_image']);
			$birthDayArr[$dobFMString][$keyFM]['uniqueKey']				=	$uniqueKey;
			$uniqueKey++;
		}
		//krsort($birthDayArr);
		ksort($birthDayArr);
		//print_r($birthDayArr);
		$a	=	0;
		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			foreach ($valueBDARR as $keySub => $valueBDArrNew) {
				$newDataArr[$a]['key']					=	$keyBDARR;
				$newDataArr[$a]['empid']				=	$valueBDArrNew['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDArrNew['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$valueBDArrNew['employeeName'];
				$newDataArr[$a]['employeeProfilePic']	=	$valueBDArrNew['employeeProfilePic'];
				
				$newDataArr[$a]['membertype']			=	$valueBDArrNew['membertype'];
				$wishExist 		=	getWishExist($_SESSION['user_empid'],$valueBDArrNew['empid'],"birthday");
				if ($valueBDArrNew['membertype']=='Colleague' && $keyBDARR==$todayString && $wishExist==false) {
					$newDataArr[$a]['isWishbtn']			=	"yes";
				} else {
					$newDataArr[$a]['isWishbtn']			=	"no";
				}
				$a++;
			}
			//print_r($valueBDARR);
		}
		$newDOBarray = array();
		$oldDOBArray = array();
		foreach ($newDataArr as $key => $dobArry) {
			$thisYearDOB= 	$dobArry['employeeDob'].' '.date(Y);
			$date 		=	date_create($thisYearDOB);
			$thisYearDOBdate = date_format($date,"Y-m-d");
			$today = date("Y-m-d");

			if($thisYearDOBdate < $today){
				array_push($oldDOBArray,$dobArry);
			} else {
				array_push($newDOBarray,$dobArry);
			}

		}
		$newDataArr = array_merge($newDOBarray,$oldDOBArray);
		echo json_encode($newDataArr);
	break;

	case 'viewAllUpcommingBirthdayList_old':

		$todayString 			=	date('md');
			
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$friendIdStr	=	implode(",",$friendList);
		
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										ORDER BY `employeeDob` ASC";

		$resProfile				=	$mycms->sql_select($sqlProfile);
		$data = array();

		$birthDayArr 		=	array();

		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile['employeeDob']));
			//if($dobString>=$todayString){
				$birthDayArr[$dobString+$key]['empid']					=	$rowProfile['id'];
				$birthDayArr[$dobString+$key]['employeeDob']				=	$rowProfile['employeeDob'];
				$birthDayArr[$dobString+$key]['employeeName']			=	$rowProfile['employeeName'];
				$profilePicPath 			=	"../upload/userprofilepicture/";
				$birthDayArr[$dobString+$key]['employeeProfilePic']	=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
				//$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
				$birthDayArr[$dobString+$key]['membertype']				=	'Colleague';
			//}
		}
		//print_r($birthDayArr);
		// $birthDayArrCount  	=	count($birthDayArr);
		// if($birthDayArrCount<3){
		// 	foreach ($resProfile as $key => $rowProfile) {
		// 		$dobString 		=	date('md',strtotime($rowProfile['employeeDob']));
		// 		if(!in_array($dobString, $birthDayArr)){
		// 			$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
		// 			$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
		// 			$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
		// 			$profilePicPath 			=	"../upload/userprofilepicture/";
		// 			$birthDayArr[$dobString]['employeeProfilePic']	=	checkFileExist($profilePicPath,$rowProfile['employeeProfilePic']);
		// 			//$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
		// 			$birthDayArr[$dobString]['membertype']				=	'Colleague';
		// 		}
		// 	}
		// }
		// print_r($birthDayArr);
		
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);

		require_once("../includes/configure.override.php");
		
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";

		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
					
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);

		swapDatabaseDisconnect(4);

		

		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily['dob']));
			$todayString 		=	date('md');

			if($dobFMString>=$todayString){
				$birthDayArr[$dobFMString]['empid']					=	$rowProfilefamily['id'];
				$birthDayArr[$dobFMString]['employeeDob']			=	$rowProfilefamily['dob'];
				$birthDayArr[$dobFMString]['employeeName']			=	$rowProfilefamily['name'];
				$birthDayArr[$dobFMString]['membertype']			=	$rowProfilefamily['member_type'];
				$familyProfilePicPath 			=	"../upload/family/";
				$birthDayArr[$dobFMString]['employeeProfilePic']	=	checkFileExist($familyProfilePicPath,$rowProfilefamily['family_image']);
				//$birthDayArr[$dobFMString]['employeeProfilePic']	=	$rowProfilefamily['family_image'];
			}
		}
		ksort($birthDayArr);
		$newDataArr 	=	array();
		$a=0;
		foreach ($birthDayArr as $keyBV => $birthDayValue) {
			if($keyBV>=$todayString){
				$newDataArr[$a]['key']					=	$keyBV;
				$newDataArr[$a]['empid']				=	$birthDayValue['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($birthDayValue['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$birthDayValue['employeeName'];
				$newDataArr[$a]['employeeProfilePic']	=	$birthDayValue['employeeProfilePic'];
				/*if($birthDayValue['employeeProfilePic']!=''){
					if(file_exists('../upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'])){
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'];
					}
					else {
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
					}
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}*/
				
				$newDataArr[$a]['membertype']			=	$birthDayValue['membertype'];
				$wishExist 		=	getWishExist($_SESSION['user_empid'],$birthDayValue['empid'],"birthday");
				if ($birthDayValue['membertype']=='Colleague' && $keyBV==$todayString && $wishExist==false) {
					$newDataArr[$a]['isWishbtn']			=	"yes";
				} else {
					$newDataArr[$a]['isWishbtn']			=	"no";
				}
				$a++;

				unset($birthDayArr[$keyBV]);
			}
		}

		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']					=	$keyBDARR;
			$newDataArr[$a]['empid']				=	$valueBDARR['empid'];
			$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDARR['employeeDob']));
			$newDataArr[$a]['employeeName']			=	$valueBDARR['employeeName'];
			$newDataArr[$a]['employeeProfilePic']	=	$valueBDARR['employeeProfilePic'];
			/*if($valueBDARR['employeeProfilePic']!=''){
				if(file_exists('../upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'])){
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'];
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
			}
			else {
				$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
			}*/
			
			$newDataArr[$a]['membertype']			=	$valueBDARR['membertype'];
			$wishExist 		=	getWishExist($_SESSION['user_empid'],$valueBDARR['empid'],"birthday");
			if ($valueBDARR['membertype']=='Colleague' && $keyBDARR==$todayString && $wishExist==false) {
				$newDataArr[$a]['isWishbtn']			=	"yes";
			} else {
				$newDataArr[$a]['isWishbtn']			=	"no";
			}
			$a++;
		}

		echo json_encode($newDataArr);

	break;
		
	/*case 'fetchbirthday':

		$todayString 			=	date('md');
		
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$friendIdStr	=	implode(",",$friendList);
		
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										ORDER BY `employeeDob` ASC";

		
		//print_r($sqlProfile);
		$resProfile				=	$mycms->sql_select($sqlProfile);
		//print_r($resProfile);
		$data = array();

		$birthDayArr 		=	array();

		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));

			if($dobString>$todayString){
				$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
				$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
				$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
				$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
				$birthDayArr[$dobString]['membertype']				=	'Colleague';
			}
		}

		$birthDayArrCount  	=	count($birthDayArr);
		if($birthDayArrCount<3){
			foreach ($resProfile as $key => $rowProfile) {
				$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));
				if(!in_array($dobString, $birthDayArr)){
					$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
					$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
					$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
					$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
					$birthDayArr[$dobString]['membertype']				=	'Colleague';
				}
			}
		}

		
		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);

		require_once("../includes/configure.override.php");
		
		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." AS `family`
											LEFT JOIN "._DB_FAMILY_MEMBER_TYPE_." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													AND   `family`.`companyId`		=		?
													ORDER BY `family`.`id` DESC";

		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',								'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],			'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_cpanelid'],		'TYP' => 's');
					
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);

		swapDatabaseDisconnect(4);

		

		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily[dob]));
			$todayString 		=	date('md');

			if($dobFMString>$todayString){
				$birthDayArr[$dobFMString]['empid']					=	$rowProfilefamily['id'];
				$birthDayArr[$dobFMString]['employeeDob']			=	$rowProfilefamily['dob'];
				$birthDayArr[$dobFMString]['employeeName']			=	$rowProfilefamily['name'];
				$birthDayArr[$dobFMString]['membertype']			=	$rowProfilefamily['member_type'];
				$birthDayArr[$dobFMString]['employeeProfilePic']	=	$rowProfilefamily['family_image'];
			}
		}

		//$newBirthArr 	=	array();
		$newDataArr 	=	array();
		$a=0;

		foreach ($birthDayArr as $keyBV => $birthDayValue) {
			if($keyBV>$todayString){
				$newDataArr[$a]['key']					=	$keyBV;
				$newDataArr[$a]['empid']				=	$birthDayValue['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($birthDayValue['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$birthDayValue['employeeName'];
				if($birthDayValue['employeeProfilePic']!=''){
					if(file_exists('../upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'])){
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'];
					}
					else {
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
					}
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
				
				$newDataArr[$a]['membertype']			=	$birthDayValue['membertype'];
				$a++;

				unset($birthDayArr[$keyBV]);
			}
		}
		

		ksort($birthDayArr);

		
		

		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']					=	$keyBDARR;
			$newDataArr[$a]['empid']				=	$valueBDARR['empid'];
			$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDARR['employeeDob']));
			$newDataArr[$a]['employeeName']			=	$valueBDARR['employeeName'];
			if($valueBDARR['employeeProfilePic']!=''){
				if(file_exists('../upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'])){
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'];
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
			}
			else {
				$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
			}
			
			$newDataArr[$a]['membertype']			=	$valueBDARR['membertype'];
			$a++;
		}
		//$sizeOfArray			=	sizeof($newDataArr);
		//$newDataArr['count']	=	$sizeOfArray;
		$newDataArrNew 			=	array_slice($newDataArr, 0,3);
		
		//print_r($birthDayArr);

		$sendResponse 			=	array();
		$sendResponse['itmes']	=	$newDataArrNew;
		$sendResponse['length']	=	count($newDataArr);

		echo json_encode($sendResponse);
	break;

	case 'viewAllUpcommingBirthdayList':

		$todayString 			=	date('md');
			
		$sqlUpcommingBirthdayFrnd			=	array();
		$sqlUpcommingBirthdayFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingBirthdayFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingBirthdayFrnd				=	$mycms->sql_select($sqlUpcommingBirthdayFrnd);
		
		foreach ($resUpcommingBirthdayFrnd as $key => $rowUpcommingBirthdayFrnd) {
			
			if($rowUpcommingBirthdayFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['empid'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['empid'];
				}
			}
			if($rowUpcommingBirthdayFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingBirthdayFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingBirthdayFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$friendIdStr	=	implode(",",$friendList);
		
		$sqlProfile				=	array();
		$sqlProfile['QUERY']	=	"SELECT `employeeDob`,
											`id`,
											`employeeName`,
											`employeeProfilePic`
										FROM "._DB_ACC_EMPLOYEE_."
										WHERE `id` IN (".$friendIdStr.")
										AND   `status` 	=	'A'
										ORDER BY `employeeDob` ASC";

		
		//print_r($sqlProfile);
		$resProfile				=	$mycms->sql_select($sqlProfile);
		//print_r($resProfile);
		$data = array();

		$birthDayArr 		=	array();

		foreach ($resProfile as $key => $rowProfile) {
			$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));

			if($dobString>$todayString){
				$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
				$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
				$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
				$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
				$birthDayArr[$dobString]['membertype']				=	'Colleague';
			}
		}

		$birthDayArrCount  	=	count($birthDayArr);
		if($birthDayArrCount<3){
			foreach ($resProfile as $key => $rowProfile) {
				$dobString 		=	date('md',strtotime($rowProfile[employeeDob]));
				if(!in_array($dobString, $birthDayArr)){
					$birthDayArr[$dobString]['empid']					=	$rowProfile['id'];
					$birthDayArr[$dobString]['employeeDob']				=	$rowProfile['employeeDob'];
					$birthDayArr[$dobString]['employeeName']			=	$rowProfile['employeeName'];
					$birthDayArr[$dobString]['employeeProfilePic']		=	$rowProfile['employeeProfilePic'];
					$birthDayArr[$dobString]['membertype']				=	'Colleague';
				}
			}
		}

		


		$sqlProfilefamily				=	array();
		$sqlProfilefamily['QUERY']		=	"SELECT `family`.`dob`,
													`family`.`id`,
													`family`.`name`,
													`family`.`family_image`,
													`familytype`.`member_type`
												FROM "._DB_ACC_EMPLOYEE_FAMILY_MEMBER." AS `family`
											LEFT JOIN "._DB_ACC_FAMILY_MEMBER_TYPE." AS `familytype`
												ON `family`.`relationship`  		= `familytype`.`id`
													WHERE `family`. `status` 		=		?
													AND   `family`.`empid`			=		?
													ORDER BY `family`.`id` DESC";

		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
		$sqlProfilefamily['PARAM'][]	=	array('FILD' => 'empid',			'DATA' => $_SESSION['user_empid'],		'TYP' => 's');
					
		$resProfilefamily				=	$mycms->sql_select($sqlProfilefamily);

		

		foreach ($resProfilefamily as $keyFM => $rowProfilefamily) {
			$dobFMString 		=	date('md',strtotime($rowProfilefamily[dob]));
			$todayString 		=	date('md');

			if($dobFMString>$todayString){
				$birthDayArr[$dobFMString]['empid']					=	$rowProfilefamily['id'];
				$birthDayArr[$dobFMString]['employeeDob']			=	$rowProfilefamily['dob'];
				$birthDayArr[$dobFMString]['employeeName']			=	$rowProfilefamily['name'];
				$birthDayArr[$dobFMString]['membertype']			=	$rowProfilefamily['member_type'];
				$birthDayArr[$dobFMString]['employeeProfilePic']	=	$rowProfilefamily['family_image'];
			}
		}

		//$newBirthArr 	=	array();
		$newDataArr 	=	array();
		$a=0;

		foreach ($birthDayArr as $keyBV => $birthDayValue) {
			if($keyBV>$todayString){
				$newDataArr[$a]['key']					=	$keyBV;
				$newDataArr[$a]['empid']				=	$birthDayValue['empid'];
				$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($birthDayValue['employeeDob']));
				$newDataArr[$a]['employeeName']			=	$birthDayValue['employeeName'];
				if($birthDayValue['employeeProfilePic']!=''){
					if(file_exists('../upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'])){
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$birthDayValue['employeeProfilePic'];
					}
					else {
						$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
					}
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
				
				$newDataArr[$a]['membertype']			=	$birthDayValue['membertype'];
				$a++;

				unset($birthDayArr[$keyBV]);
			}
		}
		

		ksort($birthDayArr);

		
		

		foreach ($birthDayArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']					=	$keyBDARR;
			$newDataArr[$a]['empid']				=	$valueBDARR['empid'];
			$newDataArr[$a]['employeeDob']			=	date('d F',strtotime($valueBDARR['employeeDob']));
			$newDataArr[$a]['employeeName']			=	$valueBDARR['employeeName'];
			if($valueBDARR['employeeProfilePic']!=''){
				if(file_exists('../upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'])){
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'upload/userprofilepicture/'.$valueBDARR['employeeProfilePic'];
				}
				else {
					$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
				}
			}
			else {
				$newDataArr[$a]['employeeProfilePic']	=	BASE_URL.'images/no_img.jpg';
			}
			
			$newDataArr[$a]['membertype']			=	$valueBDARR['membertype'];
			$a++;
		}

		//$newDataArr 	=	array_slice($newDataArr, 0,3);
		
		//print_r($birthDayArr);

		echo json_encode($newDataArr);

	break;*/

	case 'fetchanniversary':
		$friendId 			=	array();
		$anniversaryList	=	array();
		$list 				=	array();

		$sqlUpcommingAnniversaryFrnd			=	array();
		$sqlUpcommingAnniversaryFrnd['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS." 
															WHERE `status`		=	? 
															AND   (`empid`		=	?
															OR     `friend_id`	=	?)";

		$sqlUpcommingAnniversaryFrnd['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
		$sqlUpcommingAnniversaryFrnd['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlUpcommingAnniversaryFrnd['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		
		$resUpcommingAnniversaryFrnd			=	$mycms->sql_select($sqlUpcommingAnniversaryFrnd);
		
		foreach ($resUpcommingAnniversaryFrnd as $key => $rowUpcommingAnniversaryFrnd) {
			
			if($rowUpcommingAnniversaryFrnd['empid']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingAnniversaryFrnd['empid'])){

					$friendId[]		=	$rowUpcommingAnniversaryFrnd['empid'];
				}
			}
			if($rowUpcommingAnniversaryFrnd['friend_id']!=$_SESSION['user_empid']){

				if(!in_array($friendId, $rowUpcommingAnniversaryFrnd['friend_id'])){

					$friendId[]		=	$rowUpcommingAnniversaryFrnd['friend_id'];
				}
			}
		}
		$friendList		=	array_unique($friendId);

		$anniverArr 	=	array();
		$todayString 	=	date("md");

		foreach ($friendList as $keyNew => $friendRow) {
			
			$frndDetails 	=	getEmployeeDetails2($friendRow);
			if($frndDetails['anniversaryDate']){
				$annString 		=	date('md',strtotime($frndDetails['anniversaryDate']));

				//if($annString>=$todayString && $frndDetails['anniversaryDate']!=''){
					$anniverArr[$annString]['id']					=	$friendRow;
					$anniverArr[$annString]['anniversary']			=	$frndDetails['anniversaryDate'];
					$anniverArr[$annString]['name']					=	$frndDetails['name'];
					$anniverArr[$annString]['image']				=	checkFileExist("../upload/userprofilepicture/",$frndDetails['image']);
					$anniverArr[$annString]['type']					=	'Colleague';
				//}

			}
		}

		// $anniverArrCount  	=	count($anniverArr);
		// if($anniverArrCount<3){
		// 	foreach ($friendList as $key => $friendRow) {
		// 		$frndDetails 	=	getEmployeeDetails1($friendRow);
		// 		$annString 		=	date('md',strtotime($frndDetails["anniversaryDate"]));
		// 		if((!in_array($annString, $anniverArr))  && $frndDetails['anniversaryDate']!=''){
		// 			$anniverArr[$annString]['id']						=	$friendRow;
		// 			$anniverArr[$annString]['anniversary']				=	$frndDetails['anniversaryDate'];
		// 			$anniverArr[$annString]['name']						=	$frndDetails['name'];
		// 			$anniverArr[$annString]['image']					=	checkFileExist("../upload/userprofilepicture/",$frndDetails['image']);
		// 			$anniverArr[$annString]['type']						=	'Colleague';
		// 		}
		// 	}
		// }

		// $length		=	sizeof($list);

		swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);
		require_once("../includes/configure.override.php");

		$sqlFamilyMember			=	array();
		$sqlFamilyMember['QUERY']	=	"SELECT * 
												FROM "._DB_EMPLOYEE_FAMILY_MEMBER_." 
												 WHERE `empid`		=	? 
												 AND   `companyId`	=	? 
												 AND   `status`		=	?";

		$sqlFamilyMember['PARAM'][]	=	array('FILD' => 'empid', 				'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlFamilyMember['PARAM'][]	=	array('FILD' => 'empid', 				'DATA' => $_SESSION['user_cpanelid'], 	'TYP' => 's');
		$sqlFamilyMember['PARAM'][]	=	array('FILD' => 'status', 				'DATA' => 'A', 							'TYP' => 's');

		$resFamilyMember			=	$mycms->sql_select($sqlFamilyMember);
		$numFamilyMember			=	$mycms->sql_numrows($resFamilyMember);

		swapDatabaseDisconnect(4);
		
		foreach ($resFamilyMember as $keyFM => $rowProfilefamily) {
			if($rowProfilefamily["anniversaryDate"] !=""){
				$dobFMString 		=	date('md',strtotime($rowProfilefamily["anniversaryDate"]));
			
				$todayString 		=	date('md');

				//if($dobFMString>=$todayString){
					$anniverArr[$dobFMString]['id']					=	$rowProfilefamily['id'];
					$anniverArr[$dobFMString]['anniversary']		=	$rowProfilefamily['anniversaryDate'];
					$anniverArr[$dobFMString]['name']				=	$rowProfilefamily['name'];
					swapDatabaseConnect(4,DB_SERVER_MAIN,DB_SERVER_USERNAME_MAIN,DB_SERVER_PASSWORD_MAIN,DB_DATABASE_MAIN);
					$anniverArr[$dobFMString]['type']				=	getRelationship($rowProfilefamily['relationship']);
					swapDatabaseDisconnect(4);
					$anniverArr[$dobFMString]['image']				=	checkFileExist("../upload/family/",$rowProfilefamily['family_image']);
				//}
			}
		}

		ksort($anniverArr);

		$newDataArr 	=	array();
		$a=0;

		foreach ($anniverArr as $keyBV => $birthDayValue) {
			//if($keyBV>=$todayString){
				$newDataArr[$a]['key']				=	(string)$keyBV;
				$newDataArr[$a]['id']				=	$birthDayValue['id'];
				$newDataArr[$a]['anniversary']		=	date('d F',strtotime($birthDayValue['anniversary']));
				$newDataArr[$a]['name']				=	$birthDayValue['name'];
				$newDataArr[$a]['image']			=	$birthDayValue['image'];
				$newDataArr[$a]['type']				=	$birthDayValue['type'];
				$wishExist 			=	getWishExist($_SESSION['user_empid'],$birthDayValue['id'],"anniversary");
				$newDataArr[$a]['isWishbtn']		=	($birthDayValue['type']=='Colleague' && $keyBV==$todayString && $wishExist==false)?"yes":"no";
				$a++;

				unset($anniverArr[$keyBV]);
			//}
		}

		foreach ($anniverArr as $keyBDARR => $valueBDARR) {
			$newDataArr[$a]['key']				=	(string)$keyBDARR;
			$newDataArr[$a]['id']				=	$valueBDARR['id'];
			$newDataArr[$a]['anniversary']		=	date('d F',strtotime($valueBDARR['anniversary']));
			$newDataArr[$a]['name']				=	$valueBDARR['name'];
			$newDataArr[$a]['image']			=	$valueBDARR['image'];
			$newDataArr[$a]['type']				=	$valueBDARR['type'];
			$wishExist 			=	getWishExist($_SESSION['user_empid'],$valueBDARR['id'],"anniversary");
			$newDataArr[$a]['isWishbtn']		=	($valueBDARR['type']=='Colleague' && $keyBDARR==$todayString && $wishExist!=false)?"yes":"no";
			$a++;
		}

		$newAnniversaryArray = array();
		$oldAnniversaryArray = array();
		foreach ($newDataArr as $key => $dobArry) {
			$thisYearDOB= 	$dobArry['anniversary'].' '.date(Y);
			$date 		=	date_create($thisYearDOB);
			$thisYearDOBdate = date_format($date,"Y-m-d");
			$today = date("Y-m-d");

			if($thisYearDOBdate < $today){
				array_push($oldAnniversaryArray,$dobArry);
			} else {
				array_push($newAnniversaryArray,$dobArry);
			}

		}

		$newDataArr = array_merge($newAnniversaryArray,$oldAnniversaryArray);

		$newDataArrNew 			=	array_slice($newDataArr, 0,3);

		$sendResponse 			=	array();
		$sendResponse['items']	=	$newDataArrNew;
		$sendResponse['length']	=	count($newDataArr);

		echo json_encode($sendResponse);

	break;

	case 'postComment':

		$result			=	array();
		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$sqlPostComment				=	array();
		$sqlPostComment['QUERY']	=	"INSERT INTO "._DB_ACC_EMPLOYEE_POST_COMMENT."
												SET
													 `empId`			=	?,
													 `postId`			=	?,
													 `commentBody`		=	?,
													 `createdDateTime`	=	?,
													 `createdSession`	=	?,
													 `createdIp`		=	?";

		$sqlPostComment['PARAM'][]	=	array('FILD' => 'empId', 			'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlPostComment['PARAM'][]	=	array('FILD' => 'postId', 			'DATA' => $postDataArr['postId'], 		'TYP' => 's');
		$sqlPostComment['PARAM'][]	=	array('FILD' => 'commentBody', 		'DATA' => $postDataArr['commentBody'], 	'TYP' => 's');
		$sqlPostComment['PARAM'][]	=	array('FILD' => 'createdDateTime', 	'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');
		$sqlPostComment['PARAM'][]	=	array('FILD' => 'createdSession', 	'DATA' => session_id(), 				'TYP' => 's');
		$sqlPostComment['PARAM'][]	=	array('FILD' => 'createdIp', 		'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');

		$resPostComment				=	$mycms->sql_insert($sqlPostComment);

		if($resPostComment){

			$result['result']		=	'success';

			$getRecentComment				=	array();
			$getRecentComment['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
														 WHERE `id`		=	? 
														 AND   `status`	=	?";

			$getRecentComment['PARAM'][]	=	array('FILD' => 'postId', 'DATA' => $resPostComment, 	'TYP' => 's');
			$getRecentComment['PARAM'][]	=	array('FILD' => 'status', 'DATA' => 'A', 				'TYP' => 's');
			//print_r($getRecentComment);
			$resRecentComment				=	$mycms->sql_select($getRecentComment);
			$numRecentComment				=	$mycms->sql_numrows($resRecentComment);
			//print_r($resRecentComment);
			$rowRecentComment				=	$resRecentComment[0];

			$getEmpDetails		=	getEmployeeDetails1($rowRecentComment['empId']);
			$empName 			=	$getEmpDetails['name'];

			if($getEmpDetails['image']!=''){

				if(file_exists("../upload/userprofilepicture/".$getEmpDetails['image'])){

					$empPicture			=	BASE_URL."upload/userprofilepicture/".$getEmpDetails['image'];
				}
				else{

					$empPicture			=	BASE_URL."images/no_img.jpg";
				}
			}
			else{

				$empPicture			=	BASE_URL."images/no_img.jpg";
			}

			$result['id']					=	$rowRecentComment['id'];
			$result['empName']				=	$empName;
			$result['empPicture']			=	$empPicture;
			$result['empId']				=	$rowRecentComment['empId'];
			$result['commentBody']			=	$rowRecentComment['commentBody'];

			$getCommentCounter 				=	getCommentAndLikeCounter($postDataArr['postId']);

			$result['commentCount']			=	$getCommentCounter['commentCounter'];

			//********************* Send Notification **********************\\

			$getPostCreator					=	array();
			$getPostCreator['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
														WHERE  `id`		=	?
														AND	   `status`	=	?";

			$getPostCreator['PARAM'][]		=	array('FILD' => 'id', 		'DATA' => $postDataArr['postId'], 	'TYP' => 's');
			$getPostCreator['PARAM'][]		=	array('FILD' => 'status', 	'DATA' => 'A', 						'TYP' => 's');

			$resPostCreator 				=	$mycms->sql_select($getPostCreator);
			$rowPostCreator					=	$resPostCreator[0];

			$employeeId						=	$rowPostCreator['employeeId'];
			$tagFriend 						=	$rowPostCreator['tagFriend'];

			if($employeeId!=$_SESSION['user_empid']){

				$getNotification				=	notificationDetails('14');
				$notificationSubject			=	$getNotification['subject'];
				$notificationBody				=	$getNotification['body'];

				$getEmpDetails 					=	getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName 					=	$getEmpDetails['name'];

				$dataArray						=	array('{name}');
				$replaceDataArray				=	array(ucwords($getEmpName));
				$notificationBody2				=	str_replace($dataArray, $replaceDataArray, $notificationBody);
				send_notification($employeeId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "POST COMMENT", $postDataArr['postId'], 'WINDOWS', $resPostComment );
				$device_id 						=	$_SERVER['REMOTE_ADDR'];

				$fcm_token 						= 	get_fcm_id($employeeId);

				foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
					send_fcm_notification($employeeId,$_SESSION['user_empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "POST COMMENT", "POST COMMENT", $postDataArr['postId']);
				}
			}

			if ($tagFriend!='') {
				$tagFriendArr 		=	explode(",", $tagFriend);

				$getNotification				=	notificationDetails('17');
				$notificationSubject			=	$getNotification['subject'];
				$notificationBody				=	$getNotification['body'];

				$getEmpDetails 					=	getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName 					=	$getEmpDetails['name'];

				$dataArray						=	array('{name}');
				$replaceDataArray				=	array(ucwords($getEmpName));

				$notificationBody2				=	str_replace($dataArray, $replaceDataArray, $notificationBody);

				foreach ($tagFriendArr as $keyFriendArr => $valueFriendArr) {
					if ($valueFriendArr!=$_SESSION['user_empid']) {
						send_notification($valueFriendArr, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "POST COMMENT", '', $postDataArr['postId']);
						$device_id 						=	$_SERVER['REMOTE_ADDR'];

						$fcm_token 						= 	get_fcm_id($valueFriendArr);

						foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
							send_fcm_notification($valueFriendArr,$_SESSION['user_empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "POST COMMENT", "POST COMMENT", $postDataArr['postId']);
						}
					}
				}

			}
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'fetchAllComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		
		$result						=	array();
		$fetchAllComment			=	array();
		$fetchAllComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT." 
												 WHERE `postId`	=	? 
												   AND `status`	=	?";

		$fetchAllComment['PARAM'][]	=	array('FILD' => 'postId', 'DATA' => $postDataArr['postId'], 'TYP' => 's');
		$fetchAllComment['PARAM'][]	=	array('FILD' => 'status', 'DATA' => 'A', 					'TYP' => 's');

		$resAllComment				=	$mycms->sql_select($fetchAllComment);
		$numAllComment				=	$mycms->sql_numrows($resAllComment);
		if($numAllComment>0){

			foreach ($resAllComment as $key => $rowAllComment) {
				
				$getEmpDetails		=	getEmployeeDetails1($rowAllComment['empId']);
				$empName 			=	$getEmpDetails['name'];

				if($getEmpDetails['image']!=''){

					$empPicture			=	checkFileExist("../upload/userprofilepicture/",$getEmpDetails['image']);
				}
				else{

					$empPicture			=	BASE_URL."images/no_img.jpg";
				}

				$result['result']							=	'success';
				$result['comments'][$key]['id']				=	$rowAllComment['id'];
				$result['comments'][$key]['empName']		=	$empName;
				$result['comments'][$key]['empPicture']		=	$empPicture;
				$result['comments'][$key]['empId']			=	$rowAllComment['empId'];
				$result['comments'][$key]['postId']			=	$rowAllComment['postId'];
				$result['comments'][$key]['commentBody']	=	$rowAllComment['commentBody'];
			}
		}
		else{

			$result['result']					=	'success';
			$result['comments']					=	array();
		}
		echo json_encode($result);

	break;

	case 'deletePost':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$result 				=	array();
		$deletePost				=	array();
		$deletePost['QUERY']	=	"UPDATE "._DB_ACC_WALL."
										SET
											`status`	=	?
									WHERE 	`id`		=	?";

		$deletePost['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'D', 						'TYP' => 's');
		$deletePost['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $postDataArr['postId'], 	'TYP' => 's');
		//print_r($deletePost);
		$resDeletePost			=	$mycms->sql_update($deletePost);
		if($resDeletePost){

			$result['result']	=	'success';
		}
		else{

			$result['result']	=	'failed';
		}
		echo json_encode($result);

	break;

	case 'deleteComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$result 				=	array();
		$deleteComment			=	array();
		$deleteComment['QUERY']	=	"UPDATE "._DB_ACC_EMPLOYEE_POST_COMMENT."
										SET
											`status`	=	?
									WHERE 	`id`		=	?";

		$deleteComment['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'D', 							'TYP' => 's');
		$deleteComment['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $postDataArr['commentId'], 	'TYP' => 's');
		//print_r($deletePost);
		$resdeleteComment			=	$mycms->sql_update($deleteComment);
		if($resdeleteComment){

			$result['result']	=	'success';
		}
		else{

			$result['result']	=	'failed';
		}
		
		echo json_encode($result);

	break;
	case 'getemployeeList':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$friendArray			=	array();
		$getFriendId			=	array();
		$getFriendId['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS."
											WHERE  `status`		=	?
											AND    (`empid`		=	? 
											OR     `friend_id`	=	?)";

		$getFriendId['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 						'TYP' => 's');
		$getFriendId['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');
		$getFriendId['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');

		$resFriendId			=	$mycms->sql_select($getFriendId);

		foreach ($resFriendId as $key1 => $rowFriendId) {
			
			if($rowFriendId['empid']!=$_SESSION['user_empid']){

				$friendArray[]	=	$rowFriendId['empid'];
			}
			if($rowFriendId['friend_id']!=$_SESSION['user_empid']){

				$friendArray[]	=	$rowFriendId['friend_id'];
			}
		}
		$getFriendArray			=	array_unique($friendArray);

		$sqlCheck				=	array();
		$sqlCheck['QUERY']		=	"SELECT * FROM "._DB_ACC_GPS_AUTHORIZATION."
											WHERE `employeeId`		=	?
											AND   `status`			=	?";

		$sqlCheck['PARAM'][]	=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');
		$sqlCheck['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 						'TYP' => 's');
		$resCheck				=	$mycms->sql_select($sqlCheck);
		$numCheck				=	$mycms->sql_numrows($resCheck);
		if($numCheck>0){

			$accessId			=	$resCheck[0]['accessId'];
			$accessArray		=	explode(",", $accessId);
		}
		else{

			$accessId			=	'';
			$accessArray		=	explode(",", $accessId);
		}
		foreach ($getFriendArray as $key => $valueFriendArray) {
			
			if(in_array($valueFriendArray, $accessArray)){

				$newFriendArr[]	=	$valueFriendArray;
			}
		}
		$getFriendStr		=	implode(",", $newFriendArr);

		$sql 	=	array();

		$sql['PARAM'][]  	=	array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

		$where 	=	'';
		if($postDataArr['content']!=''){
			$where 	=	" AND `employeeName` LIKE ?";
			$sql['PARAM'][]  	=	array('FILD' => 'employeeName', 'DATA' => '%'.$postDataArr['content'].'%', 'TYP' => 's');
		}

		$sql['QUERY'] 		=	"SELECT * 
									FROM "._DB_ACC_EMPLOYEE_."
									WHERE `status` 	=	?
									".$where."
									AND `id`  IN (".$getFriendStr.")
									ORDER BY `employeeName` ASC";

		$res 	=	$mycms->sql_select($sql);

		$data 	=	array();
		$imagepath 		=	'../upload/userprofilepicture/';
		foreach ($res as $key => $value) {
			$data[$key]['empid'] 		=	$value['id'];
			$data[$key]['empname'] 		=	$value['employeeName'];
			$data[$key]['empimage'] 	=	checkFileExist($imagepath,$value['employeeProfilePic']);
			//$data[$key]['empimage'] 	=	$value['employeeProfilePic'];
		}

		echo json_encode($data);
	break;

	case 'getemployee':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$friendArray			=	array();
		$getFriendId			=	array();
		$getFriendId['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_FRIENDS."
											WHERE  `status`		=	?
											AND    (`empid`		=	? 
											OR     `friend_id`	=	?)";

		$getFriendId['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 						'TYP' => 's');
		$getFriendId['PARAM'][]	=	array('FILD' => 'empid', 		'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');
		$getFriendId['PARAM'][]	=	array('FILD' => 'friend_id', 	'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');

		$resFriendId			=	$mycms->sql_select($getFriendId);

		foreach ($resFriendId as $key1 => $rowFriendId) {
			
			if($rowFriendId['empid']!=$_SESSION['user_empid']){

				$friendArray[]	=	$rowFriendId['empid'];
			}
			if($rowFriendId['friend_id']!=$_SESSION['user_empid']){

				$friendArray[]	=	$rowFriendId['friend_id'];
			}
		}
		$getFriendArray		=	array_unique($friendArray);

		$getFriendStr		=	implode(",", $getFriendArray);

		$exstFrndId 		=	implode(",", $postDataArr['tagFrnd']);

		$sql 	=	array();

		$sql['PARAM'][]  	=	array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

		$where 	=	'';
		if($postDataArr['content']!=''){
			$where 	=	" AND `employeeName` LIKE ?";
			$sql['PARAM'][]  	=	array('FILD' => 'employeeName', 'DATA' => '%'.$postDataArr['content'].'%', 'TYP' => 's');
		}

		if($exstFrndId!=''){
			$where 	.=	" AND `id` NOT IN (".$exstFrndId.")";
		}

		$sql['QUERY'] 		=	"SELECT * 
									FROM "._DB_ACC_EMPLOYEE_."
									WHERE `status` 	=	?
									".$where."
									AND `id`  IN (".$getFriendStr.")
									ORDER BY `employeeName` ASC";

		$res 	=	$mycms->sql_select($sql);

		$data 	=	array();
		$imagepath 		=	'../upload/userprofilepicture/';
		foreach ($res as $key => $value) {
			$data[$key]['empid'] 		=	$value['id'];
			$data[$key]['empname'] 		=	$value['employeeName'];
			$data[$key]['empimage'] 	=	checkFileExist($imagepath,$value['employeeProfilePic']);
			//$data[$key]['empimage'] 	=	$value['employeeProfilePic'];
		}

		echo json_encode($data);
	break;

	case 'getEditData':
		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$postId 		=	$postDataArr['postId'];
		$data 			=	array();

		$sqlPostDeatils				=	array();
		$sqlPostDeatils['QUERY']	=	"SELECT * 
											FROM "._DB_ACC_WALL."
											WHERE `id`			=	?
												AND `status`	=	?";
		$sqlPostDeatils['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $postId, 	'TYP' => 's');
		$sqlPostDeatils['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 		'TYP' => 's');
		$resPostDeatils				=	$mycms->sql_select($sqlPostDeatils);
		$numPostDeatils				=	$mycms->sql_numrows($resPostDeatils);
		if ($numPostDeatils>0) {
			$rowPostDeatils			=	$resPostDeatils[0];
			$sqlWallMedia			=	array();
			$sqlWallMedia['QUERY']		=	"SELECT `media`,
													`mediaType`,
													`id` AS `mediaId`
												FROM "._DB_ACC_WALL_MEDIA."
												WHERE `wallId`	=	?
													AND `status`	=	?";
			$sqlWallMedia['PARAM'][]	=	array('FILD' => 'wallId', 	'DATA' => $rowPostDeatils['id'], 		'TYP' => 's');
			$sqlWallMedia['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 							'TYP' => 's');
			$resWallMedia				=	$mycms->sql_select($sqlWallMedia);
			$numWallMedia				=	$mycms->sql_numrows($resWallMedia);
			// print_r($sqlWallMedia);
			if ($numWallMedia>0) {
				foreach ($resWallMedia as $mediaKey => $rowWallMedia) {
					$data['media'][$mediaKey]['mediaId']	=	$rowWallMedia['mediaId'];
					$mediaTypeArray		=	explode("/", $rowWallMedia['mediaType']);
					$data['media'][$mediaKey]['mediaType']		=	$mediaTypeArray[0];
					$data['media'][$mediaKey]['mediaTypeExt']	=	$mediaTypeArray[1];
					if ($mediaTypeArray[0]=='image' || $mediaTypeArray[0] == 'video') {
						$data['media'][$mediaKey]['mediaUrl']	=	BASE_URL."upload/post/".$rowWallMedia['media'];
					} else if ($rowWallMedia['mediaType']=='youtubeLink') {
						$youtubeVideoId		=	explode("v=", $rowWallMedia['media']);
						$data['media'][$mediaKey]['mediaUrl']	=	$youtubeVideoId[1];
					}
				}
			} else {
				$data['media']	=	array();
			}
			$data['postContent']		=	$rowPostDeatils['content'];
			$data['postMedia']			=	$rowPostDeatils['media'];
			$mediatypearr 				=	explode('/', $rowPostDeatils['mediaType']);
			$data['postMediaType']		=	$mediatypearr[0];
			$data['postTagFriend']		=	$rowPostDeatils['tagFriend'];
			if($mediatypearr[0]=='youtubeLink'){
				$youtubeMedia 			=	$rowPostDeatils['media'];
				$linkCode 				=	substr($youtubeMedia, strpos($youtubeMedia, "=") + 1);
				$data['media']			=	$linkCode;
			}
			else {
				if ($mediatypearr[0]=='image') {
					$checkUnserilize 	=	@unserialize($rowPostDeatils['media']);
					if ($checkUnserilize!=false) {
						$mediaArr 		=	unserialize($rowPostDeatils['media']);
						$data['postMedia']		=	$mediaArr[0];
					} else {
						$data['postMedia']		=	$rowPostDeatils['media'];
					}
				} else if ($mediatypearr[0]=='video') {
					$data['postMedia']		=	$rowPostDeatils['media'];
				}
			}
			$tagFriendCount 			=	0;
			$keyFriendArr				=	0;
			$tagFriendArr 				=	explode(",", $rowPostDeatils['tagFriend']);
			foreach ($tagFriendArr as $tagKey => $tagFriendValue) {
				if ($tagFriendValue!="") {
					$tagFriendCount++;
					$friendDetails 				=	getEmployeeDetails1($tagFriendValue);
					$friendName 				=	$friendDetails['name'];
					$data['postTagFriendName'][$keyFriendArr]['friendName']	=	$friendName;
					$data['postTagFriendName'][$keyFriendArr]['friendid']	=	$tagFriendValue;
					$keyFriendArr++;
				} else {
					$data['postTagFriendName']	=	array();
				}
			}
			$data['postTagFriendCount']	=	$tagFriendCount;
			$data['postColourApplied']	=	$rowPostDeatils['colourApplied'];
			$data['postPrivacyStatus']	=	$rowPostDeatils['privacyStatus'];
		} else {
			$data['media']	=	array();
		}
		echo json_encode($data);
	break;

	case 'getGroupAdminWise':

		$group 					=	array();
		$sqlGroup				=	array();
		$sqlGroup['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_GROUP_TYPE." 
										 	WHERE `status`	=	? 
										 	AND   `empid`	=	?";

		$sqlGroup['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 						'TYP' => 's');
		$sqlGroup['PARAM'][]	=	array('FILD' => 'empid', 	'DATA' => $_SESSION['user_empid'], 	'TYP' => 's');
		$resGroup				=	$mycms->sql_select($sqlGroup);
		$numGroup				=	$mycms->sql_numrows($resGroup);
		if($numGroup>0){
			foreach ($resGroup as $key => $rowGroup) {
				
				$group['groups'][$key]['id']	=	$rowGroup['id'];
				$group['groups'][$key]['name']	=	$rowGroup['group_name'];
			}
		}
		else{

			$group['groups']	=	array();
		}
		echo json_encode($group);

	break;

	case 'getEditCommentBody':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$data 			=	array();

		$commentId 		=	$postDataArr['commentId'];

		$sqlGetComment				=	array();
		$sqlGetComment['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
												WHERE  `id`		=	?
												AND    `status`	=	?";

		$sqlGetComment['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $commentId, 	'TYP' => 's');
		$sqlGetComment['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 			'TYP' => 's');
		$resGetComment				=	$mycms->sql_select($sqlGetComment);
		$rowGetComment				=	$resGetComment[0];

		$data['commentBody']		=	$rowGetComment['commentBody'];

		echo json_encode($data);

	break;

	case 'editAndSubmitComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$data 			=	array();

		$commentId 		=	$postDataArr['commentId'];
		$commentBody 	=	$postDataArr['comntBody'];

		$sqlUpdate 				=	array();
		$sqlUpdate['QUERY']		=	"UPDATE "._DB_ACC_EMPLOYEE_POST_COMMENT."
										SET
											`commentBody`	=	?
									WHERE   `id`			=	?
									AND     `status`		=	?";

		$sqlUpdate['PARAM'][]	=	array('FILD' => 'commentBody', 		'DATA' => $commentBody, 	'TYP' => 's');
		$sqlUpdate['PARAM'][]	=	array('FILD' => 'id', 				'DATA' => $commentId, 		'TYP' => 's');
		$sqlUpdate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 				'TYP' => 's');
		//print_r($sqlUpdate);
		$resUpdate				=	$mycms->sql_update($sqlUpdate);

		if($resUpdate){

			$data['result']		=	'success';
		}
		else {

			$data['result']		=	'failed';
		}
		echo json_encode($data);

	break;

	case 'submitNewComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$data 			=	array();

		$commentBody 	=	$postDataArr['comment'];
		$postId 		=	$postDataArr['postId'];

		$sqlInsert 				=	array();
		$sqlInsert['QUERY']		=	"INSERT INTO "._DB_ACC_EMPLOYEE_POST_COMMENT."
											SET
												`empId`				=	?,
												`postId`			=	?,
												`commentBody`		=	?,
												`createdDateTime`	=	?,
												`createdSession`	=	?,
												`createdIp`			=	?";

		$sqlInsert['PARAM'][]	=	array('FILD' => 'empId', 			'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlInsert['PARAM'][]	=	array('FILD' => 'postId', 			'DATA' => $postId, 						'TYP' => 's');
		$sqlInsert['PARAM'][]	=	array('FILD' => 'commentBody', 		'DATA' => $commentBody, 				'TYP' => 's');
		$sqlInsert['PARAM'][]	=	array('FILD' => 'createdDateTime', 	'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');
		$sqlInsert['PARAM'][]	=	array('FILD' => 'createdSession', 	'DATA' => session_id(), 				'TYP' => 's');
		$sqlInsert['PARAM'][]	=	array('FILD' => 'createdIp', 		'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');

		$resInsert				=	$mycms->sql_insert($sqlInsert);

		if($resInsert){

			$data['result']				=	'success';
			$data['id']					=	$resInsert;

			$commentCreatorDetails 		=	getEmployeeDetails1($_SESSION['user_empid']);

			$data['empName']			=	$commentCreatorDetails['name'];
			$data['empImg']				=	checkFileExist('../upload/userprofilePicture/',$commentCreatorDetails['image']);
			$data['comment']			=	$commentBody;
			$data['isDelete']			=	'yes';
			$data['isEdit']				=	'yes';

			$getUpdatedCommentCounter	=	getCommentAndLikeCounter($postId);

			$data['commentCount']		=	$getUpdatedCommentCounter['commentCounter'];
		}
		else {

			$data['result']		=	'failed';
		}

		echo json_encode($data);

	break;

	case 'countComment':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);


		$result 		=	array();

		$commentDetails	=	getCommentAndLikeCounter($postDataArr['postid']);
		$comtCount 		=	$commentDetails['commentCounter'];

		if($comtCount>0){

			$result['result']	=	'success';
		}
		else {

			$result['result']	=	'failed';
		}
		echo json_encode($result);

	break;

	case 'commentReply':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);

		$commentReplyBody		=	$postDataArr['body'];
		$commentId 				=	$postDataArr['commentId'];
		$employeeId 			=	$_SESSION['user_empid'];

		$sqlStoreReply				=	array();
		$sqlStoreReply['QUERY']		=	"INSERT INTO "._DB_ACC_POST_COMMENT_REPLY_."
												SET 
													`commentId`			=	?,
													`employeeId`		=	?,
													`replyBody`			=	?,
													`createdDateTime`	=	?,
													`createdSession`	=	?,
													`createdIp`			=	?";

		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $commentId, 					'TYP' => 's');
		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'employeeId', 		'DATA' => $employeeId, 					'TYP' => 's');
		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'replyBody', 		'DATA' => $commentReplyBody, 			'TYP' => 's');
		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'createdDateTime', 	'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');
		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'createdSession', 	'DATA' => session_id(), 				'TYP' => 's');
		$sqlStoreReply['PARAM'][]	=	array('FILD' => 'createdIp', 		'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');
		$resStoreReply				=	$mycms->sql_insert($sqlStoreReply);
		if($resStoreReply){

			$result['result']			=	'success';

			$sqlCommentReply			=	array();
			$sqlCommentReply['QUERY']	=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_." 
													WHERE  `id`		=	?";

			$sqlCommentReply['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $resStoreReply, 					'TYP' => 's');
			$resCommentReply			=	$mycms->sql_select($sqlCommentReply);

			$employeeDetails 			=	getEmployeeDetails1($resCommentReply[0]['employeeId']);
			$employeeProfilePic			=	$employeeDetails['image'];
			$employeeName 				=	$employeeDetails['name'];

			$result['replyId']			=	$resStoreReply;
			$result['replyBody']		=	$resCommentReply[0]['replyBody'];
            $result['employeePic']		=	checkFileExist("../upload/userprofilepicture/",$employeeProfilePic);

//			$result['employeePic']		=	BASE_URL.'upload/userprofilepicture/'.$employeeProfilePic;
			$result['employeeName']		=	$employeeName;
			$result['empId']			=	$resCommentReply[0]['employeeId'];

			$sqlGetPostId				=	array();
			$sqlGetPostId['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
													WHERE  `id`			=	?
													AND    `status`		=	?";

			$sqlGetPostId['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $resCommentReply[0]['commentId'], 	'TYP' => 's');
			$sqlGetPostId['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 									'TYP' => 's');
			//print_r($sqlGetPostId);
			$resGetPostId				=	$mycms->sql_select($sqlGetPostId);
			$postId						=	$resGetPostId[0]['postId'];

			$sqlGetEmpId				=	array();
			$sqlGetEmpId['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
													WHERE  `id`		=	?
													AND    `status`	=	?";

			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'id', 			'DATA' => $resGetPostId[0]['postId'], 			'TYP' => 's');
			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'status', 		'DATA' => 'A', 									'TYP' => 's');
			$resGetEmpId				=	$mycms->sql_select($sqlGetEmpId);
			if($resGetEmpId[0]['employeeId']==$_SESSION['user_empid']){

				$result['deleteAccess']	=	'yes';
			}
			else{

				$result['deleteAccess']	=	'no';
			}

			if($result['empId']==$_SESSION['user_empid']){

				$result['isDelete']	=	'yes';
			}
			else{

				$result['isDelete']	=	'no';
			}

			if($resCommentReply[0]['employeeId']==$_SESSION['user_empid']){

				$result['editAccess']	=	'yes';
			}
			else{

				$result['editAccess']	=	'no';
			}
			//******************************************** For Notification ***************************************************//
			
			$commentPersonId		=	getCommentOwnerId($commentId);
			$postPersonId			=	getPostOwnerId($resGetPostId[0]['postId']);

			if($commentPersonId!=$_SESSION['user_empid']){

				$getNotification        =   notificationDetails('18');
				$notificationSubject    =   $getNotification['subject'];
				$notificationBody       =   $getNotification['body'];
				
				$getEmpDetails          =   getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName             =   $getEmpDetails['name'];
				
				$dataArray              =   array('{name}');
				$replaceDataArray       =   array(ucfirst($getEmpName));

				$notificationBody2      =   str_replace($dataArray, $replaceDataArray, $notificationBody);

				send_notification($commentPersonId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Comment Reply", $postId);
			
				$fcm_token              =   get_fcm_id($commentPersonId);

				foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
					send_fcm_notification($commentPersonId, $_SESSION['user_empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Custom", "Comment Reply", $postId);
				}
			}
			if($postPersonId!=$_SESSION['user_empid']){

				$getNotification1       =   notificationDetails('19');
				$notificationSubject1   =   $getNotification1['subject'];
				$notificationBody1      =   $getNotification1['body'];
				
				$getEmpDetails          =   getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName             =   $getEmpDetails['name'];

				$dataArray1             =   array('{name}');
				$replaceDataArray1      =   array(ucfirst($getEmpName));

				$notificationBody3      =   str_replace($dataArray1, $replaceDataArray1, $notificationBody1);

				send_notification($postPersonId, $_SESSION['user_empid'], $notificationSubject1, $notificationBody3, "Comment Reply", $postId);

				$fcm_token1             =   get_fcm_id($postPersonId);
				
				foreach ($fcm_token1 as $keyfcm_token_new => $valuefcm_token1) {
					send_fcm_notification($postPersonId, $_SESSION['user_empid'], $device_id, $valuefcm_token1, $notificationSubject1, $notificationBody3, "Custom", "Comment Reply", $postId);
				}
			}

			//*****************************************************************************************************************//
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'deleteCommentReply':

		$postData			=	file_get_contents("php://input");
		$postDataArr		=	json_decode($postData,true);
		$replyCommentId		=	$postDataArr['id'];
		$result				=	array();

		$sqlDeleteCommentReply				=	array();
		$sqlDeleteCommentReply['QUERY']		=	"UPDATE "._DB_ACC_POST_COMMENT_REPLY_."
													SET
														`status`		=	?
												WHERE   `id`			=	?";

		$sqlDeleteCommentReply['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 						'TYP' => 's');
		$sqlDeleteCommentReply['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $replyCommentId, 			'TYP' => 's');
		$resDeleteCommentReply				=	$mycms->sql_update($sqlDeleteCommentReply);
		if($resDeleteCommentReply){

			$result['result']			=	'success';
		}
		else{

			$result['result']			=	'failed';
		}
		echo json_encode($result);

	break;

	case 'getParticularCommentReply':

		$postData			=	file_get_contents("php://input");
		$postDataArr		=	json_decode($postData,true);
		$replyCommentId		=	$postDataArr['id'];
		$result				=	array();
		
		$sqlCommentReply			=	array();
		$sqlCommentReply['QUERY']	=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_." 
												WHERE  `id`		=	?";

		$sqlCommentReply['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $replyCommentId, 					'TYP' => 's');
		$resCommentReply			=	$mycms->sql_select($sqlCommentReply);
		if($resCommentReply){

			$result['replyBody']	=	$resCommentReply[0]['replyBody'];
		}
		echo json_encode($result);

	break;

	case 'modifyCommentReply':

		$postData			=	file_get_contents("php://input");
		$postDataArr		=	json_decode($postData,true);
		$replyCommentId		=	$postDataArr['id'];
		$replyBody 			=	$postDataArr['editedReplyBody'];
		$result				=	array();

		$sqlEditCommentReply				=	array();
		$sqlEditCommentReply['QUERY']		=	"UPDATE "._DB_ACC_POST_COMMENT_REPLY_."
													SET
														`replyBody`		=	?
												WHERE   `id`			=	?";

		$sqlEditCommentReply['PARAM'][]		=	array('FILD' => 'replyBody', 		'DATA' => $replyBody, 				'TYP' => 's');
		$sqlEditCommentReply['PARAM'][]		=	array('FILD' => 'id', 				'DATA' => $replyCommentId, 			'TYP' => 's');
		$resEditCommentReply				=	$mycms->sql_update($sqlEditCommentReply);
		if($resEditCommentReply){

			$result['result']		=	'success';
			$result['replyBody']	=	$replyBody;
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'getAllCommentReply':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$commentId 		=	$postDataArr['id'];
		$data			=	array();
		
		$sqlCommentReply			=	array();
		$sqlCommentReply['QUERY']	=	"SELECT * FROM "._DB_ACC_POST_COMMENT_REPLY_." 
												WHERE  `commentId`		=	?
												AND    `status`			=	?";

		$sqlCommentReply['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $commentId, 					'TYP' => 's');
		$sqlCommentReply['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 							'TYP' => 's');
		$resCommentReply			=	$mycms->sql_select($sqlCommentReply);

		foreach ($resCommentReply as $key => $rowCommentReply) {
			
			$data['reply'][$key]['replyId']			=	$rowCommentReply['id'];
			$data['reply'][$key]['replyBody']		=	$rowCommentReply['replyBody'];
			$data['reply'][$key]['replyType']		=	$rowCommentReply['type'];
			$employeeDetails						=	getEmployeeDetails1($rowCommentReply['employeeId']);
			$employeeProfilePic						=	$employeeDetails['image'];
			$employeeName 							=	$employeeDetails['name'];
			$data['reply'][$key]['employeeName']	=	$employeeName;
			$data['reply'][$key]['empId']			=	$rowCommentReply['employeeId'];
			$data['reply'][$key]['employeePic']		=	checkFileExist("../upload/userprofilepicture/",$employeeProfilePic);

			$sqlGetPostId				=	array();
			$sqlGetPostId['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
													WHERE  `id`			=	?
													AND    `status`		=	?";

			$sqlGetPostId['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $commentId, 				'TYP' => 's');
			$sqlGetPostId['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 						'TYP' => 's');
			$resGetPostId				=	$mycms->sql_select($sqlGetPostId);

			$sqlGetEmpId				=	array();
			$sqlGetEmpId['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
													WHERE  `id`		=	?
													AND    `status`	=	?";

			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'id', 			'DATA' => $resGetPostId[0]['postId'], 			'TYP' => 's');
			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'status', 		'DATA' => 'A', 									'TYP' => 's');
			$resGetEmpId				=	$mycms->sql_select($sqlGetEmpId);

			if($resGetEmpId[0]['employeeId']==$_SESSION['user_empid']){
				$data['reply'][$key]['isDelete']	=	'yes';
			}else{
				$data['reply'][$key]['isDelete']	=	'no';
			}

			if($rowCommentReply['employeeId']==$_SESSION['user_empid']){

				$data['reply'][$key]['deleteAccess']	=	'yes';
			}
			else{

				$data['reply'][$key]['deleteAccess']	=	'no';
			}
			if($rowCommentReply['employeeId']==$_SESSION['user_empid']){

				$data['reply'][$key]['editAccess']		=	'yes';
			}
			else{

				$data['reply'][$key]['editAccess']		=	'no';
			}
		}
		echo json_encode($data);

	break;

	case 'getAllEmoji':

		$data					=	array();
		$sqlEmoji				=	array();
		$sqlEmoji['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL_EMOJI."
											WHERE  `status`		=	?";

		$sqlEmoji['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 					'TYP' => 's');
		$resEmoji				=	$mycms->sql_select($sqlEmoji);
		foreach ($resEmoji as $key => $rowEmoji) {
			
			$data['emoji'][$key]['id']			=	$rowEmoji['id'];
			$data['emoji'][$key]['name']		=	$rowEmoji['name'];
		}
		echo json_encode($data);

	break;

	case 'commentPostWithEmoji':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$emojiId		=	$postDataArr['emojiId'];
		$postId 		=	$postDataArr['postId'];
		
		$sqlGetEmojiName				=	array();
		$sqlGetEmojiName['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL_EMOJI."
													WHERE  `id`		=	?";

		$sqlGetEmojiName['PARAM'][]		=	array('FILD' => 'id', 		'DATA' => $emojiId, 		'TYP' => 's');
		$resGetEmojiName				=	$mycms->sql_select($sqlGetEmojiName);
		$emojiName 						=	$resGetEmojiName[0]['name'];

		$insertIntoComment				=	array();
		$insertIntoComment['QUERY']		=	"INSERT INTO "._DB_ACC_EMPLOYEE_POST_COMMENT."
													SET
														`empId`				=	?,
														`postId`			=	?,
														`commentBody`		=	?,
														`type`				=	?,
														`createdDateTime`	=	?,
														`createdSession`	=	?,
														`createdIp`			=	?";

		$insertIntoComment['PARAM'][]	=	array('FILD' => 'empId', 				'DATA' => $_SESSION['user_empid'], 			'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'postId', 				'DATA' => $postId, 							'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'commentBody', 			'DATA' => $emojiName, 						'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'type', 				'DATA' => 'image', 							'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdDateTime', 		'DATA' => date('Y-m-d H:i:s'), 				'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdSession', 		'DATA' => session_id(), 					'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdIp', 			'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
		$resIntoComment					=	$mycms->sql_insert($insertIntoComment);
		if($resIntoComment){

			$employeeDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
			$employeeProfilePic			=	$employeeDetails['image'];
			$employeeName 				=	$employeeDetails['name'];

			$sqlGetEmpId				=	array();
			$sqlGetEmpId['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
													WHERE  `id`		=	?
													AND    `status`	=	?";

			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'id', 			'DATA' => $postId, 			'TYP' => 's');
			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'status', 		'DATA' => 'A', 				'TYP' => 's');
			$resGetEmpId				=	$mycms->sql_select($sqlGetEmpId);

			$result['result']			=	'success';
			$result['id']				=	$resIntoComment;
			$result['commentBody']		=	BASE_URL.'images/smiley/'.$emojiName;
			$result['employeeName']		=	$employeeName;
			$result['employeePic']		=	BASE_URL.'upload/userprofilepicture/'.$employeeProfilePic;
			if($resGetEmpId[0]['employeeId']==$_SESSION['user_empid']){

				$result['deleteAccess']		=	'yes';
			}
			else{

				$result['deleteAccess']		=	'no';
			}
			//******************************************** For Notification ***************************************************//

			$postPersonId			=	getPostOwnerId($postId);
			
			if($postPersonId!=$_SESSION['user_empid']){

				$getNotification        =   notificationDetails('14');
				$notificationSubject    =   $getNotification['subject'];
				$notificationBody       =   $getNotification['body'];
				
				$getEmpName             =   $employeeDetails['name'];
				
				$dataArray              =   array('{name}');
				$replaceDataArray       =   array(ucfirst($getEmpName));

				$notificationBody2      =   str_replace($dataArray, $replaceDataArray, $notificationBody);

				send_notification($postPersonId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "POST COMMENT", $postId);
			
				$fcm_token              =   get_fcm_id($postPersonId);

				foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
					send_fcm_notification($_SESSION['user_empid'], $postPersonId, $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Post Comment", "Post Comment", $postPersonId);
				}
			}

			//*****************************************************************************************************************//
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'commentReplyWithEmoji':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$emojiId		=	$postDataArr['emojiId'];
		$commentId 		=	$postDataArr['commentId'];
		$postId 		=	$postDataArr['postId'];
		$result			=	array();

		$sqlGetEmojiName				=	array();
		$sqlGetEmojiName['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL_EMOJI."
													WHERE  `id`		=	?";

		$sqlGetEmojiName['PARAM'][]		=	array('FILD' => 'id', 		'DATA' => $emojiId, 		'TYP' => 's');
		$resGetEmojiName				=	$mycms->sql_select($sqlGetEmojiName);
		$emojiName 						=	$resGetEmojiName[0]['name'];

		$insertIntoComment				=	array();
		$insertIntoComment['QUERY']		=	"INSERT INTO "._DB_ACC_POST_COMMENT_REPLY_."
													SET
														`commentId`			=	?,
														`employeeId`		=	?,
														`replyBody`			=	?,
														`type`				=	?,
														`createdDateTime`	=	?,
														`createdSession`	=	?,
														`createdIp`			=	?";

		$insertIntoComment['PARAM'][]	=	array('FILD' => 'commentId', 			'DATA' => $commentId, 						'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'employeeId', 			'DATA' => $_SESSION['user_empid'], 			'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'replyBody', 			'DATA' => $emojiName, 						'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'type', 				'DATA' => 'image', 							'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdDateTime', 		'DATA' => date('Y-m-d H:i:s'), 				'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdSession', 		'DATA' => session_id(), 					'TYP' => 's');
		$insertIntoComment['PARAM'][]	=	array('FILD' => 'createdIp', 			'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
		$resIntoComment					=	$mycms->sql_insert($insertIntoComment);
		if($resIntoComment){

			$employeeDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
			$employeeProfilePic			=	$employeeDetails['image'];
			$employeeName 				=	$employeeDetails['name'];

			$sqlGetEmpId				=	array();
			$sqlGetEmpId['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
													WHERE  `id`		=	?
													AND    `status`	=	?";

			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'id', 			'DATA' => $postId, 			'TYP' => 's');
			$sqlGetEmpId['PARAM'][]		=	array('FILD' => 'status', 		'DATA' => 'A', 				'TYP' => 's');
			$resGetEmpId				=	$mycms->sql_select($sqlGetEmpId);

			$result['result']			=	'success';
			$result['replyId']			=	$resIntoComment;
			$result['replyBody']		=	BASE_URL.'images/smiley/'.$emojiName;
			$result['type']				=	'image';
			$result['employeeName']		=	$employeeName;
			$result['empId']			=	$_SESSION['user_empid'];
			$result['employeePic']		=	BASE_URL.'upload/userprofilepicture/'.$employeeProfilePic;
			if($resGetEmpId[0]['employeeId']==$_SESSION['user_empid']){

				$result['deleteAccess']		=	'yes';
			}
			else{

				$result['deleteAccess']		=	'no';
			}
			//******************************************** For Notification ***************************************************//
			
			$commentPersonId		=	getCommentOwnerId($commentId);
			$postPersonId			=	getPostOwnerId($resGetPostId[0]['postId']);

			if($commentPersonId!=$_SESSION['user_empid']){

				$getNotification        =   notificationDetails('18');
				$notificationSubject    =   $getNotification['subject'];
				$notificationBody       =   $getNotification['body'];
				
				$getEmpDetails          =   getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName             =   $getEmpDetails['name'];
				
				$dataArray              =   array('{name}');
				$replaceDataArray       =   array(ucfirst($getEmpName));

				$notificationBody2      =   str_replace($dataArray, $replaceDataArray, $notificationBody);

				send_notification($commentPersonId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Comment Reply", $postId);
			
				$fcm_token              =   get_fcm_id($commentPersonId);

				foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
					send_fcm_notification($commentPersonId, $_SESSION['user_empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Custom", "Comment Reply", $postId);
				}
			}
			if($postPersonId!=$_SESSION['user_empid']){

				$getNotification1       =   notificationDetails('19');
				$notificationSubject1   =   $getNotification1['subject'];
				$notificationBody1      =   $getNotification1['body'];
				
				$getEmpDetails          =   getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName             =   $getEmpDetails['name'];

				$dataArray1             =   array('{name}');
				$replaceDataArray1      =   array(ucfirst($getEmpName));

				$notificationBody3      =   str_replace($dataArray1, $replaceDataArray1, $notificationBody1);

				send_notification($postPersonId, $_SESSION['user_empid'], $notificationSubject1, $notificationBody3, "Comment Reply", $postId);

				$fcm_token1             =   get_fcm_id($postPersonId);
				
				foreach ($fcm_token1 as $keyfcm_token_new => $valuefcm_token1) {
					send_fcm_notification($postPersonId, $_SESSION['user_empid'], $device_id, $valuefcm_token1, $notificationSubject1, $notificationBody3, "Custom", "Comment Reply", $postId);
				}
			}

			//*****************************************************************************************************************//
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'likeCommentCase':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$commentId 		=	$postDataArr['id'];
		$postId 		=	$postDataArr['postId'];
		$result			=	array();

		$sqlLikeComment				=	array();
		$sqlLikeComment['QUERY']	=	"INSERT INTO "._DB_ACC_POST_COMMENT_LIKE_."
												SET
													`commentId`			=	?,
													`employeeId`		=	?,
													`createdDateTime`	=	?,
													`createdSession`	=	?,
													`createdIp`			=	?";

		$sqlLikeComment['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $commentId, 					'TYP' => 's');
		$sqlLikeComment['PARAM'][]	=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$sqlLikeComment['PARAM'][]	=	array('FILD' => 'createdDateTime', 	'DATA' => date('Y-m-d H:i:s'), 			'TYP' => 's');
		$sqlLikeComment['PARAM'][]	=	array('FILD' => 'createdSession', 	'DATA' => session_id(), 				'TYP' => 's');
		$sqlLikeComment['PARAM'][]	=	array('FILD' => 'createdIp', 		'DATA' => $_SERVER['REMOTE_ADDR'], 		'TYP' => 's');
		$resLikeComment				=	$mycms->sql_insert($sqlLikeComment);
		if($resLikeComment){

			$result['result']		=	'success';

			//******************************************** For Notification ***************************************************//

			$commentPersonId		=	getCommentOwnerId($commentId);

			if($commentPersonId!=$_SESSION['user_empid']){

				$getNotification        =   notificationDetails('20');
				$notificationSubject    =   $getNotification['subject'];
				$notificationBody       =   $getNotification['body'];
				
				$getEmpDetails          =   getEmployeeDetails1($_SESSION['user_empid']);
				$getEmpName             =   $getEmpDetails['name'];
				
				$dataArray              =   array('{name}');
				$replaceDataArray       =   array(ucfirst($getEmpName));

				$notificationBody2      =   str_replace($dataArray, $replaceDataArray, $notificationBody);

				send_notification($commentPersonId, $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Comment Like", $postId,'WINDOWS',$commentId);
			
				/*$fcm_token              =   get_fcm_id($commentPersonId);

				foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
					send_fcm_notification($_SESSION['user_empid'], $commentPersonId, $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "Comment Reply", "Comment Reply", $commentPersonId);
				}*/
			}

			//*****************************************************************************************************************//
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'unlikeCommentCase':

		$postData		=	file_get_contents("php://input");
		$postDataArr	=	json_decode($postData,true);
		$commentId 		=	$postDataArr['id'];
		$result			=	array();

		$sqlUnlikeComment				=	array();
		$sqlUnlikeComment['QUERY']		=	"UPDATE "._DB_ACC_POST_COMMENT_LIKE_."
												SET
													`status`		=	?
											WHERE   `commentId`		=	?
											AND     `employeeId`	=	?";

		$sqlUnlikeComment['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'D', 							'TYP' => 's');
		$sqlUnlikeComment['PARAM'][]	=	array('FILD' => 'commentId', 		'DATA' => $commentId, 					'TYP' => 's');
		$sqlUnlikeComment['PARAM'][]	=	array('FILD' => 'employeeId', 		'DATA' => $_SESSION['user_empid'], 		'TYP' => 's');
		$resUnlikeComment				=	$mycms->sql_update($sqlUnlikeComment);
		if($resUnlikeComment){

			$result['result']		=	'success';
		}
		else{

			$result['result']		=	'failed';
		}
		echo json_encode($result);

	break;

	case 'postBirthWish':
		$postData 		=	file_get_contents("php://input");
		$postDataArr 	=	json_decode($postData,true);

		$sqlPost 		=	array();
		$sqlPost['QUERY']	=	"INSERT INTO "._DB_ACC_WALL."
									SET 
										`employeeId`		=	?,
										`content`			=	?,
										`tagFriend`			=	?,
										`privacyStatus`		=	?,
										`createdDate`		=	?,
										`createdSession`	=	?,
										`createdIp`			=	?";

		$sqlPost['PARAM'][] 	=	array("FILD" => "employeeId", 		"DATA" => $_SESSION['user_empid'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "content", 			"DATA" => $postDataArr['content'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "tagFriend", 		"DATA" => $postDataArr['empid'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "privacyStatus", 	"DATA" => "tag", 						"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdDate", 		"DATA" => date("Y-m-d H:i:s"), 			"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdSession",	"DATA" => session_id(), 				"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdIp", 		"DATA" => $_SERVER['REMOTE_ADDR'], 		"TYP" => "s");

		$resPost 				=	$mycms->sql_insert($sqlPost);

		$sqlWish 				=	array();
		$sqlWish['QUERY']		=	"INSERT INTO "._DB_ACC_BIRTH_ANNIVERSARY_WISH_."
										SET 
											`wishBy`		=	?,
											`wishTo`		=	?,
											`wishType`		=	?,
											`content`		=	?,
											`createdDate`	=	?,
											`createdIp`		=	?";

		$sqlWish['PARAM'][] 	=	array("FILD" => "wishBy", 			"DATA" => $_SESSION['user_empid'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "wishTo", 			"DATA" => $postDataArr['empid'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "wishType", 		"DATA" => "birthday", 					"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "content", 			"DATA" => $postDataArr['content'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "createdDate", 		"DATA" => date("Y-m-d H:i:s"), 			"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "createdIp", 		"DATA" => $_SERVER['REMOTE_ADDR'], 		"TYP" => "s");

		$resWish 				=	$mycms->sql_insert($sqlWish);

		$getNotification		=	notificationDetails('24');
		$notificationSubject	=	$getNotification['subject'];
		$notificationBody		=	$getNotification['body'];

		$getEmpDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
		$getEmpName 			=	$getEmpDetails['name'];

		$dataArray				=	array('{name}','{content}');
		$replaceDataArray		=	array(ucwords($getEmpName),$postDataArr['content']);

		$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);

		send_notification($postDataArr['empid'], $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Birthday Wish", $resPost);

		$device_id 				=	$_SERVER['REMOTE_ADDR'];

		$fcm_token 				= 	get_fcm_id($postDataArr['empid']);
		//print_r($fcm_token);
		foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
			send_fcm_notification($_SESSION['user_empid'], $postDataArr['empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "BirthdayWish", "Birthday Wish", $resPost);
		}
		
		echo 'success';
		break;


	case 'postAnniversaryWish':
		$postData 		=	file_get_contents("php://input");
		$postDataArr 	=	json_decode($postData,true);

		$sqlPost 		=	array();
		$sqlPost['QUERY']	=	"INSERT INTO "._DB_ACC_WALL."
									SET 
										`employeeId`		=	?,
										`content`			=	?,
										`tagFriend`			=	?,
										`privacyStatus`		=	?,
										`createdDate`		=	?,
										`createdSession`	=	?,
										`createdIp`			=	?";

		$sqlPost['PARAM'][] 	=	array("FILD" => "employeeId", 		"DATA" => $_SESSION['user_empid'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "content", 			"DATA" => $postDataArr['content'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "tagFriend", 		"DATA" => $postDataArr['empid'], 		"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "privacyStatus", 	"DATA" => "tag", 						"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdDate", 		"DATA" => date("Y-m-d H:i:s"), 			"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdSession",	"DATA" => session_id(), 				"TYP" => "s");
		$sqlPost['PARAM'][] 	=	array("FILD" => "createdIp", 		"DATA" => $_SERVER['REMOTE_ADDR'], 		"TYP" => "s");

		$resPost 				=	$mycms->sql_insert($sqlPost);

		$sqlWish 				=	array();
		$sqlWish['QUERY']		=	"INSERT INTO "._DB_ACC_BIRTH_ANNIVERSARY_WISH_."
										SET 
											`wishBy`		=	?,
											`wishTo`		=	?,
											`wishType`		=	?,
											`content`		=	?,
											`createdDate`	=	?,
											`createdIp`		=	?";

		$sqlWish['PARAM'][] 	=	array("FILD" => "wishBy", 			"DATA" => $_SESSION['user_empid'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "wishTo", 			"DATA" => $postDataArr['empid'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "wishType", 		"DATA" => "anniversary", 				"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "content", 			"DATA" => $postDataArr['content'], 		"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "createdDate", 		"DATA" => date("Y-m-d H:i:s"), 			"TYP" => "s");
		$sqlWish['PARAM'][] 	=	array("FILD" => "createdIp", 		"DATA" => $_SERVER['REMOTE_ADDR'], 		"TYP" => "s");

		$resWish 				=	$mycms->sql_insert($sqlWish);

		$getNotification		=	notificationDetails('25');
		$notificationSubject	=	$getNotification['subject'];
		$notificationBody		=	$getNotification['body'];

		$getEmpDetails 			=	getEmployeeDetails1($_SESSION['user_empid']);
		$getEmpName 			=	$getEmpDetails['name'];

		$dataArray				=	array('{name}','{content}');
		$replaceDataArray		=	array(ucwords($getEmpName),$postDataArr['content']);

		$notificationBody2		=	str_replace($dataArray, $replaceDataArray, $notificationBody);

		send_notification($postDataArr['empid'], $_SESSION['user_empid'], $notificationSubject, $notificationBody2, "Anniversary Wish", $resPost);

		$device_id 				=	$_SERVER['REMOTE_ADDR'];

		$fcm_token 				= 	get_fcm_id($postDataArr['empid']);
		//print_r($fcm_token);
		foreach ($fcm_token as $keyfcm_token => $valuefcm_token) {
			send_fcm_notification($_SESSION['user_empid'], $postDataArr['empid'], $device_id, $valuefcm_token, $notificationSubject, $notificationBody2, "AnniversaryWish", "Anniversary Wish", $resPost);
		}
		
		echo 'success';
		break;

	default:
	     echo "ERROR !!";
	break;
}


function getPostLikeSatus($empId,$postId){
	global $mycms;

	$sql 	=	array();
	$sql['QUERY']		=	"SELECT * 
								FROM "._DB_ACC_WALL_LIKE_."
								WHERE  `empId` 		=	?
								AND    `postId` 	=	?
								AND    `status` 	=	?";

	$sql['PARAM'][]	=	array('FILD' => 'empId',			'DATA' => $empId,				'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'postId',			'DATA' => $postId,				'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',					'TYP' => 's');
	//print_r($sql);
	$res 			=	$mycms->sql_select($sql);
	$num 			=	$mycms->sql_numrows($res);

	if($num>0){
		$result 	=	'true';
	}
	else {
		$result 	=	'false';
	}

	return $result;
}

function getCommentAndLikeCounter($postId){

	global $mycms;

	$likeIdArray				=	array();
	$likeIdWithOutMeArray       =   array();
	$commentIdArray				=	array();
	$resultArray				=	array();
	$commentArray				=	array();

	$sqlLike 			=	array();
	$sqlLike['QUERY']	=	"SELECT * 
								FROM "._DB_ACC_WALL_LIKE_."
								WHERE  `postId` 	=	?
								AND    `status` 	=	?";

	$sqlLike['PARAM'][]	=	array('FILD' => 'postId',			'DATA' => $postId,						'TYP' => 's');
	$sqlLike['PARAM'][]	=	array('FILD' => 'status',			'DATA' => 'A',							'TYP' => 's');
	
	$resLike 			=	$mycms->sql_select($sqlLike);
	$numLike 			=	$mycms->sql_numrows($resLike);

	foreach ($resLike as $key => $rowLike) {
		
		$likeIdArray[]	=	$rowLike['empId'];
	}
	foreach ($resLike as $keyLike => $valueLike) {
        if ($_SESSION['user_empid']!=$valueLike['empId']) {
           $likeIdWithOutMeArray[] =   $valueLike['empId'];
        }
    }

	$sqlComment 			=	array();
	$sqlComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
										WHERE 	`postId`		=	?
										AND 	`status`		=	?";

	$sqlComment['PARAM'][]	=	array('FILD' => 'postId', 	'DATA' => $postId, 					'TYP' => 's');
	$sqlComment['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 						'TYP' => 's');

	$resComment 			=	$mycms->sql_select($sqlComment);
	$numComment 			=	$mycms->sql_numrows($resComment);

	foreach ($resComment as $key => $rowComment) {
		
		$commentIdArray[]	=	$rowComment['empId'];
	}

	if(in_array($_SESSION['user_empid'], $likeIdArray)){

		$resultArray['yourLike']		=	'yes';
		$resultArray['likeCounter']		=	(string)$numLike-1;
	}
	else{

		$resultArray['yourLike']		=	'no';
		$resultArray['likeCounter']		=	(string)$numLike;
	}

	$resultArray['otherLike']           =   $likeIdWithOutMeArray;

	if(in_array($_SESSION['user_empid'], $commentIdArray)){

		$resultArray['yourComment']		=	'yes';
		$resultArray['commentCounter']	=	(string)$numComment;
	}
	else{

		$resultArray['yourComment']		=	'no';
		$resultArray['commentCounter']	=	(string)$numComment;
	}
	return $resultArray;
}

function getAllCommentPostWise($postId){

	global $mycms;

	$comment 				=	array();

	$sqlComment 			=	array();
	$sqlComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
										WHERE  `postId`		=	?
										AND    `status`		=	?
									ORDER BY `id` DESC
									LIMIT 0,2";

	$sqlComment['PARAM'][]	=	array('FILD' => 'postId', 		'DATA' => $postId, 					'TYP' => 's');
	$sqlComment['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 						'TYP' => 's');
	//print_r($sqlComment);
	$resComment 			=	array_reverse($mycms->sql_select($sqlComment));
	$numComment 			=	$mycms->sql_numrows($resComment);
	if($numComment>0){

		foreach ($resComment as $key => $rowComment) {
			
			$comment['comment'][$key]['id']			=	$rowComment['id'];
			$comment['comment'][$key]['body']		=	$rowComment['commentBody'];
			$comment['comment'][$key]['empId']		=	$rowComment['empId'];

			$employeeDetails 						=	getEmployeeDetails1($rowComment['empId']);
			$employeeImage							=	$employeeDetails['image'];
			$employeeName							=	$employeeDetails['name'];

			$comment['comment'][$key]['empName']	=	$employeeName;

			if($employeeImage!=''){

				$filePath		=	"../upload/userprofilepicture/";
				$comment['comment'][$key]['empImg']		=	checkFileExist($filePath,$employeeImage);
			}

			if($rowComment['empId']==$_SESSION['user_empid']){

				$comment['comment'][$key]['deleteAccess']	=	'yes';
				$comment['comment'][$key]['editAccess']		=	'yes';
			}
			else {

				$comment['comment'][$key]['deleteAccess']	=	'no';
				$comment['comment'][$key]['editAccess']		=	'no';
			}
		}
	}
	else {

		$comment['comment']		=	array();
	}
	return $comment;
}
function getAllCommentPostWiseNew($postId){

	global $mycms;

	$comment 				=	array();

	$sqlComment 			=	array();
	$sqlComment['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
										WHERE  `postId`		=	?
										AND    `status`		=	?
									ORDER BY `id` DESC";

	$sqlComment['PARAM'][]	=	array('FILD' => 'postId', 		'DATA' => $postId, 					'TYP' => 's');
	$sqlComment['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 						'TYP' => 's');
	//print_r($sqlComment);
	$resComment 			=	array_reverse($mycms->sql_select($sqlComment));
	$numComment 			=	$mycms->sql_numrows($resComment);
	if($numComment>0){

		foreach ($resComment as $key => $rowComment) {
			
			$comment['comment'][$key]['id']			=	$rowComment['id'];
			$comment['comment'][$key]['body']		=	$rowComment['commentBody'];

			$employeeDetails 						=	getEmployeeDetails1($rowComment['empId']);
			$employeeImage							=	$employeeDetails['image'];
			$employeeName							=	$employeeDetails['name'];

			$comment['comment'][$key]['empName']	=	$employeeName;

			if($employeeImage!=''){

				$filePath		=	"../upload/userprofilepicture/";
				$comment['comment'][$key]['empImg']		=	checkFileExist($filePath,$employeeImage);
			}

			if($rowComment['empId']==$_SESSION['user_empid']){

				$comment['comment'][$key]['deleteAccess']	=	'yes';
				$comment['comment'][$key]['editAccess']		=	'yes';
			}
			else {

				$comment['comment'][$key]['deleteAccess']	=	'no';
				$comment['comment'][$key]['editAccess']		=	'no';
			}
		}
	}
	else {

		$comment['comment']		=	array();
	}
	return $comment;
}

function getCommentReplyCommentCounter($commentId){

	global $mycms;

	$result									=	array();
	$commentReplyEmployeeId					=	array();
	$sqlCommentReplyCounter					=	array();
	$sqlCommentReplyCounter['QUERY']		=	"SELECT `employeeId` FROM "._DB_ACC_POST_COMMENT_REPLY_."
														WHERE  `commentId`		=	?
														AND    `status`			=	?
													GROUP BY   `employeeId`";

	$sqlCommentReplyCounter['PARAM'][]		=	array('FILD' => 'commentId', 	'DATA' => $commentId, 					'TYP' => 's');
	$sqlCommentReplyCounter['PARAM'][]		=	array('FILD' => 'status', 		'DATA' => 'A', 							'TYP' => 's');
	$resCommentReplyCounter					=	$mycms->sql_select($sqlCommentReplyCounter);
	$numCommentReplyCounter					=	$mycms->sql_numrows($resCommentReplyCounter);
	foreach ($resCommentReplyCounter as $key => $commentReplyValue) {
		
		$commentReplyEmployeeId[]		=	$commentReplyValue['employeeId'];
	}
	if(in_array($_SESSION['user_empid'],$commentReplyEmployeeId)){

		$result['myComment']		=	'yes';
		$result['commentCounter']	=	$numCommentReplyCounter-1;
	}
	else{

		$result['myComment']		=	'no';
		$result['commentCounter']	=	$numCommentReplyCounter;
	}
	return $result;
}

function getCommentOwnerId($commentId){

	global $mycms;

	$sqlUserId				=	array();
	$sqlUserId['QUERY']		=	"SELECT * FROM "._DB_ACC_EMPLOYEE_POST_COMMENT."
										WHERE  `id`		=	?";
	$sqlUserId['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $commentId, 'TYP' => 's');
	$resUserId				=	$mycms->sql_select($sqlUserId);

	return $resUserId[0]['empId'];
}
function getPostOwnerId($postId){

	global $mycms;

	$sqlUserId				=	array();
	$sqlUserId['QUERY']		=	"SELECT * FROM "._DB_ACC_WALL."
										WHERE  `id`		=	?";
	$sqlUserId['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $postId, 'TYP' => 's');
	$resUserId				=	$mycms->sql_select($sqlUserId);

	return $resUserId[0]['employeeId'];
}

/////////////////////////// FUNCTION /////////////////////
function getBranctIdByEmployeeId($userId) {
    global $mycms;

    $sqlSearchCranchId             =   array();
    $sqlSearchCranchId['QUERY']    =   "SELECT `employeeBranchId`
                                            FROM "._DB_ACC_EMPLOYEE_."
                                            WHERE  `id`         =   ?
                                            AND    `status`     =   ?
                                            ORDER BY `id` DESC";

    $sqlSearchCranchId['PARAM'][]  =   array('FILD' => 'id',       'DATA' => $userId,      'TYP' => 's');
    $sqlSearchCranchId['PARAM'][]  =   array('FILD' => 'status',   'DATA' => 'A',          'TYP' => 's');
    
    $resSearchCranchId             =   $mycms->sql_select($sqlSearchCranchId);

    return $resSearchCranchId[0]['employeeBranchId'];
}



function getEmployeeDetails2($empId){

	global $mycms;
	$sqlDetails				=	array();
	$sqlDetails['QUERY']	=	"SELECT * FROM "._DB_ACC_EMPLOYEE_." 
	WHERE `id`	=	?
	AND (`employeeCurrentStatus` = '' OR `employeeCurrentStatus` IS NULL OR `employeeCurrentStatus` = 'Active' OR `employeeCurrentStatus` = 'joining' OR `employeeCurrentStatus` = 'offerletter')";
	//  AND `status`	=	?
	$sqlDetails['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $empId, 	'TYP' => 's');
	// $sqlDetails['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'A', 		'TYP' => 's');
	$resDetails				=	$mycms->sql_select($sqlDetails);
	$rowDetails				=	$resDetails[0];

	$empDetails 						=	array();
    $empDetails['id']					=	$rowDetails['id'];
	$empDetails['name']					=	$rowDetails['employeeName'];
	$empDetails['dob']					=	$rowDetails['employeeDob'];
	$empDetails['email']				=	$rowDetails['employeeEmail'];
	$empDetails['mobile']				=	$rowDetails['employeeMobile'];
	$empDetails['address']				=	$rowDetails['employeeAddress'];
	$empDetails['pincode']				=	$rowDetails['employeePincode'];
	$empDetails['image']				=	$rowDetails['employeeProfilePic'];
	$empDetails['doj']					=	$rowDetails['employeeDoj'];
	$empDetails['pan']					=	$rowDetails['employeePAN'];
	$empDetails['employeeUAN']			=	$rowDetails['employeeUAN'];
	$empDetails['code']					=	$rowDetails['employeeCode'];
	$empDetails['empDept']				=	$rowDetails['employeeDepartment'];
	$empDetails['empDeptId']			=	$rowDetails['employeeDepartmentId'];
	$empDetails['empDesig']				=	$rowDetails['employeeDesignation'];
	$empDetails['gender']				=	$rowDetails['employeeGender'];
  $empDetails['bankName']				=	$rowDetails['bankName'];
  $empDetails['accNumber']			=	$rowDetails['accNumber'];
  $empDetails['accountType']			=	$rowDetails['accountType'];
  $empDetails['IFSCnum']			    =	$rowDetails['IFSCnum'];
  $empDetails['branchName']			=	$rowDetails['branchName'];
	$empDetails['anniversaryDate']		=	$rowDetails['anniversaryDate'];
	$empDetails['empBranchId']			=	$rowDetails['employeeBranchId'];
	$empDetails['marriedStatus']		=	($rowDetails['anniversaryDate']!='')?true:false;
	$empDetails['emailVisibility']		=	$rowDetails['emailVisibility'];
	$empDetails['mobileVisibility']		=	$rowDetails['mobileVisibility'];
	$empDetails['subCompanyId']		=	$rowDetails['employeeSubCompanyId'];
	$empDetails['HOD']			=	$rowDetails['HOD'];
	$empDetails['currentStatus']		=	$rowDetails['employeeCurrentStatus'];
	return $empDetails;
}


function  GetMACAddress(){
	ob_start();
	$MAC =exec('getmac');
	ob_clean();
	$MAC =strtok($MAC,' ');

	//echo "$MAC"."hhhhh";
}
// {
//     ob_start();
//     system('getmac');
//     $Content = ob_get_contents();
//     ob_clean();
//     return substr($Content, strpos($Content,'\\')-20, 17);
// }


function getDefaultClockInOutTime($employeeId){
	global $mycms;
	$clockIn = '';
	$clockOut = '';
	$response =array();
	$sqlList               =               array();
        $sqlList['PARAM'][]    =       array("FILD" => 'status',         "DATA" =>'A',              "TYP" => "s");
        $sqlList['PARAM'][]    =       array("FILD" => "employeeId",     "DATA" =>$employeeId,     "TYP" => "s");

        $sqlList['QUERY']      =   "SELECT `attendanceType`,`shiftId` FROM 
                                        "._DB_EMPLOYEE_DETAILS_." 
                                            WHERE   `status` =   ?
                                            AND   `employeeId` =   ?
                                            
                                            AND attendanceType IS NOT NULL";
        $resList            =   $mycms->sql_select($sqlList);
        $numList            =   $mycms->sql_numrows($resList);
        if($numList > 0){

            if($resList[0]['attendanceType'] == 'shift_wise'){

                $sqlShift   =  array();
                $sqlShift['QUERY']     =   "SELECT * FROM 
                                            "._DB_EMPLOYEE_SHIFT_MASTER_."
                                        WHERE    `id`        =   ?
                                        AND      `status`    =   ?
                                        ";

                $sqlShift['PARAM'][]    =       array("FILD" => "id",             "DATA" =>$resList[0]['shiftId'],               "TYP" => "s");
                $sqlShift['PARAM'][]    =       array("FILD" => "status",         "DATA" =>'A',                                  "TYP" => "s");

                $resShift           =   $mycms->sql_select($sqlShift);

                $clockIn            =  $resShift[0]['clockInTime'];
                $clockOut           =  $resShift[0]['clockOutTime'];
            }else if($resList[0]['attendanceType'] == 'customize'){

                $sqlShiftCustomize    =   array();
                $sqlShiftCustomize['QUERY']     =   "SELECT * FROM 
                                    "._DB_EMPLOYEE_SHIFT_CUSTOMIZED_."  
                                        WHERE   `status` =   ?
                                        AND     `employeeId`    =   ?
                                        ";

                $sqlShiftCustomize['PARAM'][]    =       array("FILD" => "status",                 "DATA" =>'A',                                  "TYP" => "s");
                $sqlShiftCustomize['PARAM'][]    =       array("FILD" => "employeeId",             "DATA" =>$employeeId,               "TYP" => "s");

                $resShiftCustomize            =   $mycms->sql_select($sqlShiftCustomize);

                $clockIn   =  $resShiftCustomize[0]['clockInTime'];
                $clockOut  =  $resShiftCustomize[0]['clockOutTime'];
            }else if($resList[0]['attendanceType'] == 'roaster'){


                $sqlRoasater               =   array();
                $sqlRoasater['QUERY']       = "SELECT * FROM 
                                                "._DB_EMPLOYEE_ROASTER_."  
                                                    WHERE   `status`        =   ?
                                                    AND     `employeeId`    =   ?
                                                    ";

                $sqlRoasater['PARAM'][]    =       array("FILD" => "status",                "DATA" =>'A',                                      "TYP" => "s");
                $sqlRoasater['PARAM'][]    =       array("FILD" => "employeeId",            "DATA" =>$employeeId,                "TYP" => "s");


                $resRoasater            =   $mycms->sql_select($sqlRoasater);

                foreach ($resRoasater as $key2 => $value2) {

                    $sqlShiftDetails   =  array();
                    $sqlShiftDetails['QUERY']     =   "SELECT * FROM 
                                                "._DB_EMPLOYEE_SHIFT_MASTER_."
                                            WHERE    `id`        =   ?
                                            AND      `status`    =   ?
                                            ";

                    $sqlShiftDetails['PARAM'][]    =       array("FILD" => "id",             "DATA" =>$value2['shiftId'],               "TYP" => "s");
                    $sqlShiftDetails['PARAM'][]    =       array("FILD" => "status",         "DATA" =>'A',                                  "TYP" => "s");

                    $resShiftDetails            =   $mycms->sql_select($sqlShiftDetails);
                    $clockIn  =  $resShiftDetails[0]['clockInTime'];
                    $clockOut =  $resShiftDetails[0]['clockOutTime'];
                
                }
            }
        }

        



        $response['clockIn'] = $clockIn;
        $response['clockOut'] = $clockOut;
	
        return $clockIn;

}
?>

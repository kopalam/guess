<?php

class MessageEntity  {

    function getComemntCount($user_id,$UserType)
        {
            $commentUser = ['conditions'=> $UserType." = ".$user_id];
            $commentUserData = GetMessage::find( $commentUser )->toArray();

            if(empty($commentUserData))
            {
                $fragmentCommentData    =   0;
                return $fragmentCommentData;
            }

            foreach ($commentUserData as $key => $value) {
                
                $find = ['conditions'=>'article_id = '.$value['article_id'].' and comment_user_id !='.$user_id.' and dates >'.$value['dates']];

                $message = GetMessage::find( $find )->toArray();

            }


         
            if(empty($message))
                 {
           

                    $count = 0;
                    return $count;

                 }

                   foreach ($message as $key => $value) {
                    //找出status= 0 的，写入 is_read 表，user_id 对应自己的id，comment_user_id =新评论者的id，status = 1
                    $finder = ['conditions'=>'article_id = '.$value['article_id'].' and dates ='.$value['dates'].' and user_id = '.$user_id];
                    $checkRead  =   IsRead::findFirst( $finder );

                    if(empty($checkRead))
                    {
                        $isRead     =   new IsRead(); 
                        $isRead->user_id = $user_id;
                        $isRead->comment_user_id = $value['comment_user_id'];
                        $isRead->status  =  1;
                        $isRead->dates   =  $value['dates'];
                        $isRead->article_id    =   $value['article_id'];
                        $isRead->save();
                    }
                    


                }


      
                $User = ['conditions'=> "user_id = ".$user_id." and status = 1"];
                $UserData = IsRead::find( $User )->toArray();

                // print_r($UserData);exit();
                $count = count($UserData);
                // $count = $count == $statusCount ?$count-1:$count;
                return $count;
            }

    function getArticleCount($user_id,$UserType)
    {
        /*原创者的统计*/
        $commentUser =  ['conditions'=> $UserType." = ".$user_id];
        $commentUserData = GetMessage::find( $commentUser )->toArray();


        
        if(empty($commentUserData))
        {
                $count = 0;
                return $count;

        }

        foreach ($commentUserData as $key => $value) {
            $find = ['conditions'=>'article_id = '.$value['article_id']];
            $message = GetMessage::find( $find )->toArray();

             
        }

      
        if(empty($message))
             {
       

                $count = 0;
                return $count;

             }

             foreach ($message as $key => $value) {
                //找出status= 0 的，写入 is_read 表，user_id 对应自己的id，comment_user_id =新评论者的id，status = 1
                $checkRead  =   UserRead::findFirst( array('conditions'=>'article_id = '.$value['article_id'].' and dates ='.$value['dates']) );
                if(empty($checkRead))
                {
                    $isRead     =   new UserRead(); 
                    $isRead->user_id = $user_id;
                    $isRead->comment_user_id = $value['comment_user_id'];
                    $isRead->status  =  1;
                    $isRead->dates   =  $value['dates'];
                    $isRead->article_id    =   $value['article_id'];
                    $isRead->save();
                } 
                
                //查找出isRead表中，与我user_id对应的fragment_id是否为1，如果为1则是未读，需要打印出来，为0 则是已读
                $findIsRead = ['conditions'=>'article_id = '.$value['article_id'].' and status = 1 and user_id ='.$user_id];
                $readStatus = UserRead::find( $findIsRead )->toArray();

            }


            $count = count($readStatus);
            return $count;
        }

        function getData($user_id,$UserType)
        {


             $commentUser = ['conditions'=> $UserType." = ".$user_id];
             $commentUserData = GetMessage::find( $commentUser )->toArray();

             if(empty($commentUserData))
                $fragmentCommentData    =   0;


             foreach ($commentUserData as $key => $value) {
            
            $find = ['conditions'=>'article_id = '.$value['article_id'].' and user_id ='.$user_id];

            $message = GetMessage::find( $find )->toArray();

        }
      // print_r($message);exit();
            if(empty($message))
                 {
                     $fragmentCommentData = 0;
                     return $fragmentCommentData;
                 }



              foreach ($message as $key => $value) {
                //找出status= 0 的，写入 is_read 表，user_id 对应自己的id，comment_user_id =新评论者的id，status = 1

                //查找出isRead表中，与我user_id对应的fragment_id是否为1，如果为1则是未读，需要打印出来，为0 则是已读
                $findIsRead = ['conditions'=>'article_id = '.$value['article_id'].' and status = 1 and user_id ='.$user_id];
                $readStatus = UserRead::find( $findIsRead )->toArray();

            }
                if(empty($readStatus))
                    return 0;

                    foreach ($readStatus as $key => $value) {
                        
                            $parameters['conditions'] = 'id = '.$value['article_id'];
                                $fragment   =   Issued::find( $parameters )->toArray();

                              foreach ($fragment as $k => $v) {

                                 $fragmentCommentData[$key]['topicId'] = $v['id'];
                                 $fragmentCommentData[$key]['contents'] = $v['contents'];
                                 $fragmentCommentData[$key]['status'] = $value['status'];
                                 $fragmentCommentData[$key]['des'] =  $UserType =='comment_user_id' ? 'comment_user':'fragment_user';
                    }

                                    $slide = IssuedSlide::find( ['conditions'=>'topic_id = '.$value['article_id']] )->toArray();
                                        if(empty($slide))
                                             $fragmentCommentData[$key]['images'] = 0;
                                foreach ($slide as $get => $img) {
                                    $fragmentCommentData[$key]['images'] = $img['slide'];
                                }
                    
             

                    
                }
                $data = $this->second_array_unique_bykey($fragmentCommentData,'topicId');
                return $data;
            // return $fragmentCommentData;

    }


    function getCommentData($user_id)
    {
         //--新的评论通知--
        $commentUser = ['conditions'=> 'user_id = '.$user_id.' and status = 1'];
        $commentUserData = IsRead::find( $commentUser )->toArray();

        // print_r($commentUserData);exit();
        // return $commentUserData;
        $fragmentCommentData = array();
         if(empty($commentUserData))
        {
            $fragmentCommentData    =   0;
            return $fragmentCommentData;
        }

        foreach ($commentUserData as $key => $value) {
                
                    $parameters['conditions'] = 'id = '.$value['article_id'];
                        $fragment   =   Issued::find( $parameters )->toArray();

                      foreach ($fragment as $k => $v) {

                         $fragmentCommentData[$key]['topicId'] = $v['id'];
                         $fragmentCommentData[$key]['title'] = $v['title'];
                         $fragmentCommentData[$key]['status'] = $value['status'];
                         $fragmentCommentData[$key]['des'] = 'comment_user';
            }
                         $slide = IssuedSlide::find( ['conditions'=>'topic_id = '.$value['article_id']] )->toArray();
                         if(empty($slide))
                                $fragmentCommentData[$key]['images'] = 0;
                                foreach ($slide as $get => $img) {
                                    $fragmentCommentData[$key]['images'] = $img['slide'];
                                }
                    
            
        }

        // print_r($fragmentCommentData);exit()
        $data = $this->second_array_unique_bykey($fragmentCommentData,'topicId');
        return $data;
    }

    function second_array_unique_bykey($arr, $key){  
        $tmp_arr = array();  
        foreach($arr as $k => $v)  
        {  
                if(in_array($v[$key], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true  
                {  
                    unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值  
                }  
                else {  
                    $tmp_arr[$k] = $v[$key];  //将不同的值放在该数组中保存  
                }  
        }  
           //ksort($arr); //ksort函数对数组进行排序(保留原键值key)  sort为不保留key值  
            return $arr;  
    }  

}
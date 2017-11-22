<?php

use Phalcon\Mvc\Controller;

class UploadController extends Controller
{

    /*图片上传，针对 发布框上传图片*/
    public function uploadImageAction()
    {
       // 定义上传路径
         // $data['status']   = 0;
         // $data['message']  = '';
        // print_r($_FILES);exit();
        //  $upload  = new UploadImg($_FILES);
        //  $info    = $upload->getImage();
        // print_r($info);
        // exit();
        try{
            $upload  = new UploadImg();
            $info    = $upload->getImage();

            // $user = Users::findFirst($user_id);

            $image = "/public/upload".$info['dirname']."/".$info['filename'];
           
                // throw new Exception( $error, 1);


        }catch(Exception $e){
            $data['status']  = 1;
            $data['message'] = $e->getMessage();
            Utils::apiDisplay( $data );
        }

        

        $data = ['src' => $image];
        Utils::apiDisplay(['status'=>0,'data'=>$data]);
    }

   

    
 }
    
?>


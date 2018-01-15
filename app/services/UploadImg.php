<?php

class UploadImg{

     public $upload_name;                    //上传文件名

    public $upload_tmp_name;                //上传临时文件名

    public $upload_final_name;              //上传文件的最终文件名

    public $upload_target_dir = '../public/upload';              //文件被上传到的目标目录

    public $upload_target_file;             //文件被上传到的最终路径

    public $upload_filetype ;               //上传文件类型

     public $allow_upload_type = [
        "image/png","image/jpeg","image/jpg"
    ];

    public $upload_file_size;               //上传文件的大小
    public $allow_uploaded_maxsize = 10000000;    //允许上传文件的最大值

    public function __construct(){

        $this->upload_name       = $_FILES["image"]["name"]; //取得上传文件名

        $this->upload_filetype   = $_FILES['image']['type'];

        $this->upload_tmp_name   = $_FILES["image"]["tmp_name"];

        $this->upload_file_size  = $_FILES["image"]["size"];


    }

    public function getImage(){

        // return 'halo';
        if( !$this->isAllowFile( $this->upload_filetype ) )
            throw new Exception('不允许上传该类型', 10001);

        if( !$this->isAllowSize( $this->upload_file_size ) )
            throw new Exception("文件大小异常！", 10001);

        $dir_name      = $this->createUploadDir();
        //$new_file_name = $this->ceateFileName( $this->upload_name );

        $new_file_name = uniqid().".png";

        $this->upload_target_file = $dir_name."/".$new_file_name;
        // echo $this->upload_tmp_name;
        // print_r($_FILES);exit();
        if(!move_uploaded_file($this->upload_tmp_name,$this->upload_target_file))
            throw new Exception("文件上传失败！", 10001);

        return [
            "path"     => $this->upload_target_file,
            "dirname"  => str_replace($this->upload_target_dir, "", $dir_name),

            "filename" => $new_file_name
            // "filename"=>$new_file_name
        ];
    }


    protected function isAllowFile( $file_type ){
        $info = pathinfo($file_name);
        // return in_array($file_type,$this->allow_upload_type)?true:false;
        return true;

    }

    protected function isAllowSize( $size ){
        return $size < $this->allow_uploaded_maxsize ? true:false;
    }

    protected function createUploadDir(){
        $dir_name = $this->upload_target_dir."/".date("YmdH");
        if(! is_dir($dir_name)){
            mkdir($dir_name,0777,true);
            chmod($dir_name,0777);
        }
        return $dir_name;
    }

    protected function ceateFileName( $file_name ){
        $info = pathinfo($file_name);
        return uniqid().".".$info["extension"];
    }

   /**
    *获取文件扩展名
    *@param String $filename 要获取文件名的文件
    */
   public function getFileExt($filename){
        $info = pathinfo($filename);
        return $info["extension"];
   }

}
?>

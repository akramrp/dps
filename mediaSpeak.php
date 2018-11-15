<?php
class mediaSpeak extends core
{
    use Paginator;
    static $publish_date,$newspaper,$edition_city,$year,$status,$desc,$img_src,$catimg,$catimg_thumb,$msId=null;
    public function addMediaSpeak(){
        self::$publish_date=Request::post('publish_date');
        self::$newspaper=Request::post('newspaper');
        self::$edition_city=Request::post('edition_city');
        self::$year=Request::post('year');
        self::$status=Request::post('status');
        self::$desc=Request::post('desc');
        
            if(self::$img_src['name']){
               self::addImage(self::$img_src); 
            }
            
            $values["publish_date"] = self::SQLValue(self::$publish_date,self::SQLVALUE_TEXT);
            $values["newspaper"] = self::SQLValue(self::$newspaper,self::SQLVALUE_TEXT);
            $values["edition_city"] = self::SQLValue(self::$edition_city,self::SQLVALUE_TEXT);
            $values["year"] = self::SQLValue(self::$year,self::SQLVALUE_TEXT);
            $values["img_src"] = self::SQLValue(self::$catimg,self::SQLVALUE_TEXT);
            $values["main_image"] = self::SQLValue(self::$catimg,self::SQLVALUE_TEXT);
            $values["thumb_image"] = self::SQLValue(self::$catimg_thumb,self::SQLVALUE_TEXT);
            $values["status"] = self::SQLValue(self::$status,self::SQLVALUE_TEXT);
            $values["descr"] = self::SQLValue(self::$desc,self::SQLVALUE_TEXT); 
            
            if($this->Query(self::BuildSQLInsert('media_speak',$values))){
                //echo "<script>alert('hiii')</script>";
                return true;
            }else{ return false; }

    }
    public function updateGaCategory(){
        
        self::$publish_date=Request::post('publish_date');
        self::$newspaper=Request::post('newspaper');
        self::$edition_city=Request::post('edition_city');
        self::$year=Request::post('year');
        self::$status=Request::post('status');
        self::$desc=Request::post('desc');
        self::$msId=Request::post('msId');
        // $where['category_name']=self::SQLValue(self::$cat_name,self::SQLVALUE_TEXT);
        // array_push($where,'id!='.self::$gacatID);
        // $this->Query(self::getRecordsQuery('gallery_category',$where));
        // if($this->Error()) {echo "We have an error: ".$this->Error();die;}
        //if(!$total_records=$this->RowCount()){
            if(self::$img_src['name']){
               echo self::addImage(self::$img_src);
            }

            $values["publish_date"] = self::SQLValue(self::$publish_date,self::SQLVALUE_TEXT);
            $values["newspaper"] = self::SQLValue(self::$newspaper,self::SQLVALUE_TEXT);
            $values["edition_city"] = self::SQLValue(self::$edition_city,self::SQLVALUE_TEXT);
            $values["year"] = self::SQLValue(self::$year,self::SQLVALUE_TEXT);
            $values["img_src"] = self::SQLValue(self::$catimg,self::SQLVALUE_TEXT);
            //print_r(self::$img_src); echo print_r(self::$catimg); print_r(self::$catimg_thumb); die;
            if(self::$img_src && self::$catimg && self::$catimg_thumb) {
                $values["main_image"] = self::SQLValue(self::$catimg, self::SQLVALUE_TEXT);
                $values["thumb_image"] = self::SQLValue(self::$catimg_thumb, self::SQLVALUE_TEXT);
            }
            $values["status"] = self::SQLValue(self::$status,self::SQLVALUE_TEXT);
            $values["descr"] = self::SQLValue(self::$desc,self::SQLVALUE_TEXT); 


            $filter["id"] = self::SQLValue(self::$msId,self::SQLVALUE_NUMBER);
            if($this->Query(self::BuildSQLUpdate('media_speak',$values,$filter))){
                return true;
            }else{return false;}

        // }else{
        //     $_SESSION['Error_msg']='Category Name Already Exit';
        // }

    }
    public  static function addImage($image){
        $store = "../media/speak/";
        if (!file_exists($store))
        {
            mkdir($store,0777);
        }
        $thumb="../media/speak/thumb/";
        if (!file_exists($thumb))
        {
            mkdir($thumb,0777);
        }

        $handle = new Upload($image);
        if ($handle->uploaded) {
            $handle->image_resize = true;
            $handle->image_ratio_y = true;
            $handle->image_x = $handle->image_src_x;
            $handle->Process($store);
            if ($handle->processed) {
                self::$catimg=str_replace('../','',$store).$handle->file_dst_name;
            }


            $handle->image_resize = true;
            $handle->image_x = 360;
            $handle->image_y = 240;

            $handle->Process($thumb);

            // we check if everything went OK
            if ($handle->processed) {
                self::$catimg_thumb=str_replace('../','',$thumb).$handle->file_dst_name;
        }
            // we delete the temporary files
            $handle->Clean();

        }
    }
}

$mediaSpeakobj=new mediaSpeak();


if ((isset($_POST['submit'])) && ($_POST['submit']=='submit')){
    $mediaSpeakobj::$img_src=$_FILES['my_field'];
       if($mediaSpeakobj->addMediaSpeak()){

           $_SESSION['SuccessMsg']='Media Speak Added Successfully';
           $mediaSpeakobj::redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
           exit();
       }
}



if ((isset($_POST['submit'])) && ($_POST['submit']=='Update')){
    $mediaSpeakobj::$img_src=$_FILES['my_field'];

    if($mediaSpeakobj->updateGaCategory()){

        $_SESSION['SuccessMsg']='Media Speak Updated Successfully';
        $mediaSpeakobj::redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }
}



if($mediaSpeakobj::getParam('action')){
    if($mediaSpeakobj->checkColumnValue('media_speak','id',$_GET['gaCatId'])){
        if($_GET['page']>1){$actionPageUtm='?page='.$_GET['page']; }else{$actionPageUtm='';}
        if($_GET['action']=='gaCatDelete'){
            $filter['id']= $mediaSpeakobj::SQLValue($_GET['gaCatId'],$mediaSpeakobj::SQLVALUE_NUMBER);
            if($mediaSpeakobj->DeleteRows('media_speak',$filter)){
                $_SESSION['SuccessMsg']='Category Delete Successfully';
                $mediaSpeakobj::redirect(ADMIN_URL.$_GET['url'].'/'.$actionPageUtm);
                exit();
            }else{echo "Not Working";}

        }elseif ($_GET['action']=='gaCatstatus'){
            if($_GET['statusaction']=='enabled'){$status='1';}else{$status='0';}
            $values['status']=$mediaSpeakobj::SQLValue($status,$mediaSpeakobj::SQLVALUE_TEXT);
            $filter['id']=$mediaSpeakobj::SQLValue($_GET['gaCatId'],$mediaSpeakobj::SQLVALUE_TEXT);
            if($mediaSpeakobj->Query($mediaSpeakobj::BuildSQLUpdate('media_speak',$values,$filter))){
                $_SESSION['SuccessMsg']='Status Change Successfully';
                $mediaSpeakobj::redirect(ADMIN_URL.$_GET['url'].'/'.$actionPageUtm);
                exit();
            }
        }
    }else{
        $mediaSpeakobj::redirect(ADMIN_URL.$_GET['url'].'/'.$actionPageUtm);
    }


}
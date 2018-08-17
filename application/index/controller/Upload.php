<?php
namespace app\index\controller;
//require '../vendor/autoload.php';
class Upload extends PublicController
{
    public function request($name){
        $func =$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(3,$name,false);
        $this->$func();
    }

    private function getUploadConfig($id=''){
        $data=[];
        $user= db('user')->where(['id'=>$id])->find();
        $this->user=$user;
        $res = $this->getUserDataByApi($this->user,'getWebconfig',$data);
        if(!$res['data']){
            JCode(ERROR,"获取信息错误");
        }
        return $res['data'];
    }

    private function sendFileToUploadServer($type, $filename='', $base64='') {
//        $user = checkSession();
//        if (!$user) {
//            JCode(40000, "登录超时!请重新登录!");
//        }
        // 判断文件后缀
        $upfilename = $_FILES['fileList']['name'];
        $tempfile = $_FILES['fileList']['tmp_name'];
        $ext = substr($upfilename, strpos($upfilename, "."), 4);
        $head = "";
        if ($ext == '.jpg') {
            $head = 'data:image/jpg;base64,';
        }else if ($ext == '.png') {
            $head = 'data:image/png;base64,';
        }else{
            JCode(1, "仅支持.jpg和.png文件上传!");
        }


        $conf =$this->getUploadConfig(input('id'));
        if ( empty($conf) || empty($conf['upload_url']) || empty($conf['website'])) {
            JCode(2, "没有找到上传服务器配置!");
        }
        $filename = $filename == '' ? md5_file($tempfile) : $filename;
        $destfile = getcwd()."/upload/{$filename}";
        try{
            if (!move_uploaded_file($tempfile, $destfile)) {
                throw new \Exception("上传文件失败", 1);

            }
        }catch(\Exception $e) {
            JCode(3, "权限不足!",$e);
        }

        // 读入文件字节集


        $s3 = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'ap-northeast-1',
            'credentials' => [
                'key' => 'AKIAI3QUNVBQCWAKK6BQ',
                'secret' => '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',
            ]
        ]);

        // 测试上传文件
        try {
            $s3->putObject([
                'Bucket' => 'uploads.bxvip588.com',
                'Key'    => getUploadPath($type, $conf['website'])."/{$filename}{$ext}",
                'Body'   => fopen($destfile, "r"),
            ]);
        } catch (Aws\S3\Exception\S3Exception $e) {
            cpLog("上传文件到S3服务器失败!", 'StaticController', array("Key"=>getUploadPath($type, $conf['website'])."/{$filename}{$ext}", "Body"=>$destfile, "isEditor"=>I("get.editorid",false)), GRAYLOG_ERROR, null);
            JCode(1, "上传文件失败!");
        }

        unlink($destfile);
        if (input("get.editorid",false)) {
            $pic_src = $conf['upload_url'].'/'.getUploadPath($type, $conf['website'])."/{$filename}{$ext}";
        }else{
            $pic_src = "{$filename}{$ext}";
        }
        JCode(0, "msgok", $pic_src);
    }

    // 上传网站LOGO
    public function UploadWebLogo() {
        $this->sendFileToUploadServer(7, 'pc_logo');
    }

    // 上传APPLOGO
    public function UploadPhoneLogo() {
        $this->sendFileToUploadServer(7, 'phone_logo');
    }

}
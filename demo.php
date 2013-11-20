<?php
/* 调用PHPMailer发送电邮
* @param  String  $receiver     收件人
* @param  String  $sender       发件人
* @param  String  $sender_name  发件人名称如为空则用发件人地址代替
* @param  String  $subject      邮件主题
* @param  String  $content      邮件内容
* @param  boolean $ishtml       是否html电邮
* @param  Array   $attachements 附件
* @return boolean
*/
function sendMail($receiver, $sender, $sender_name='', $subject, $content, $ishtml=true, $attachments=array()) {
    include_once "class-phpmailer.php"; 

    if(empty($receiver) || empty($sender) || empty($subject) || empty($content)){
        return false;
    }

    if(trim($sender_name)==''){
        $sender_name = $sender;
    }
    
    $mail = new PHPMailer();

    #$mail->IsSMTP();                 // 经smtp发送 
    #$mail->Host = "smtp.gmail.com";  // SMTP 服务器
    #$mail->Port = 465;               // SMTP 端口
    #$mail->SMTPSecure = 'ssl';       // 加密方式
    #$mail->SMTPAuth = true;          // 打开SMTP认证
    #$mail->Username = "username";    // 用户名
    #$mail->Password = "password";    // 密码

    $mail->IsMail();                  // using PHP mail() function 有可能會出現這封郵件可能不是由以下使用者所傳送的提示

    $mail->From     = $sender;        // 发信人
    $mail->FromName = $sender_name;   // 发信人别名
    $mail->AddReplyTo($sender);       // 回覆人
    $mail->AddAddress($receiver);     // 收信人

    // 以html方式发送
    if($ishtml){
        $mail->IsHTML(true);
    }

    // 发送附件
    if($attachments){
        if(is_array($attachments)){
            $send_attachments = array();

            $tmp_attachments = array_slice($attachments,0,1);
            if(!is_array(array_pop($tmp_attachments))){
                if(isset($attachments['path'])){
                    array_push($send_attachments, $attachments);
                }else{
                    foreach($attachments as $attachment){
                        array_push($send_attachments, array('path'=>$attachment));
                    }
                }
            }else{
                $send_attachments = $attachments;
            }

            foreach($send_attachments as $attachment){
                $attachment['name'] = isset($attachment['name'])? $attachment['name'] : null;
                $attachment['encoding'] = isset($attachment['encoding'])? $attachment['encoding'] : 'base64';
                $attachment['type'] = isset($attachment['type'])? $attachment['type'] : 'application/octet-stream';
                if(isset($attachment['path']) && file_exists($attachment['path'])){
                    $mail->AddAttachment($attachment['path'],$attachment['name'],$attachment['encoding'],$attachment['type']);
                }
            }
        }elseif(is_string($attachments)){
            if(file_exists($attachments)){
                $mail->AddAttachment($attachments);
            }
        }
    }

    $mail->Subject  = $subject;    // 邮件标题
    $mail->Body     = $content;    // 邮件內容
    return $mail->Send();  
}


$receiver = 'receiver@test.com';
$sender = 'sender@test.com';
$sender_name = 'sender name';
$subject = 'subjecct';
$content = 'content';

// 四種格式都可以
$attachments = 'attachment1.jpg';
$attachments = array('path'=>'attachment1.jpg', 'name'=>'附件1.jpg');
$attachments = array('attachment1.jpg','attachment2.jpg','attachment3.jpg');
$attachments = array(
    array('path'=>'attachment1.jpg', 'name'=>'附件1.jpg'),
    array('path'=>'attachment2.jpg', 'name'=>'附件2.jpg'),
    array('path'=>'attachment3.jpg', 'name'=>'附件3.jpg'),
);

$flag = sendMail($receiver, $sender, $sender_name, $subject, $content, true, $attachments);
echo $flag;

?>
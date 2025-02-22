<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<hr class="hr" />
<table class="mytable">
<tbody>
<tr><th colspan="2">宫殿信息</th></tr>
<?php 
  if(empty($result)){
     echo '<tr><th colspan="2">未加入宫殿</th></tr>';
  }else{
      echo '<tr><th>宫殿id</th><td>'.$result['id'].'</td></tr>';
      echo '<tr><th>宫殿名称</th><td>'.$result['name'].'</td></tr>';
      echo '<tr><th>宫殿密码</th><td>'.$password.'</td></tr>';
      echo '<tr><th>宫殿等级</th><td>'.$result['level'].'</td></tr>';
      echo '<tr><th>宫殿经验</th><td>'.$result['exp'].'</td></tr>';
      echo '<tr><th>宫殿财富</th><td>'.$result['fund'].'</td></tr>';
      foreach ($result['members'] as $user){
          switch ($user['post']){
              //1:宫主  2:副宫主 3:尚宫 4:成员 5:其他
              case 1:
                  $post = '宫主';
                  break;
              case 2:
                  $post = '副宫主';
                  break;
              case 3:
                  $post = '尚宫';
                  break;
              case 4:
                  $post = '成员';
                  break;
              default:
                  $post = '其他';
                  break;
          }
          echo '<tr>';
          echo '<th>'.$post.'</th>';
          echo '<td><a href="?sevid='.$SevidCfg['sevid'].'&mod=user&act=userChange&uid='.$user['id'].'">'.$user['id'].'</a>(';
          echo '<a href="?sevid='.$SevidCfg['sevid'].'&mod=user&act=userChange&uid='.$user['id'].'">'.$user['name'].'</a>)</td>';
          echo '</tr>';
      }
  }
?>
</tbody>
</table>
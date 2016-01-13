## 开发帮助
### page使用
 - 命名规则
   - 文件名称为 目录 + index.php 根目录为index.php, user目录为user.index.php, user/accout目录为user.account.index.php
   - 类名命名规则，文件名'.'替换为'_', index.php类名为index, user.index.php类名为user_index
   - 类中method及对应url方法， 方法'post_'为保留前缀，代表该方法需要用post提交数据
   - method参数及url必须传递的参数，可选参数使用参数默认值就行
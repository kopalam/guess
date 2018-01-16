#看图猜餐厅#

--
**技术规范**

```
接口地址: https://sale.imchaotan.com/guess
对接数据库:dotka  

常用写法:驼峰写法 如 getUserInfoAction 
接口规范: 
转化为json数组-> status=0,message='',data = [数组];
```

**答题流程概要**  

```
通过sid进入到界面，
先检查该用户在prize_logs表中是否已经参与过该活动，如果已经参与过，则跳转到结果页。否则写入该用户的uid，活动sid到logs表中。
先从memcached查询该sid是否有题库缓存，如果有，则直接获取题库并传递到前端，如果缓存无增写入缓存。
倒计时时间：10s
倒计时后，停留3秒。出现正确答案与用户选择答案。
根据首页传来的fid(活动id)，再从题库中选取还有fid的题目和答案。
题目随机选择，数据包含：
@题目图片+题目+正确答案+随机（含正确选项）3个

数据库设计
表：guess_collection 创建活动
|名称   |类型 	|参数|
|----|------|----|
|id	    | int    |3|
|name   | varchar| 60| 活动名称
|dates | int| 20| 创建时间
|logo_img    | varchar 	 |130| 活动logo，如果没有就使用默认
|bg_img	|varchar |120| 背景图片,如果没有就使用默认

表：guess_quesstion 题目
|名称   |类型 	|参数|
|----|------|----|
|id	    | int    |3|
|tile   | varchar| 60|
|answer | varchar| 60|
|sid    | int 	 |3|
|img	|varchar |120|


表：guess_selects 随机选项
|名称   |类型 	|参数|
|----|------|----|
|id	    | int    |3|
|qid   | int| 20| 对应的题目id
|sid    | int 	 |3|
|selects | varchar| 60|
|status | int |2| 默认0


```

**答题完成流程**

```
答题完成后，检测guess_prize对应sid中，奖品数量amount还有多少，如果少于等于0，则显示已送完，并且更改status状态更改为1。
查询status=1，则显示活动已结束。
表：guess_prize 随机选项
|名称   |类型 	|参数|
|----|------|----|
|id	    | int    |3|
|sid    | int 	 |3|
|prize_name | varchar| 60|
|prize_amount | int| 4|
|status | int |2| 默认 0

```

**领取记录**

```
答题完成的用户，记录领取奖品份数，用户uid，答题完成时间，活动sid
表：guess_prize_logs 随机选项
|名称   |类型 	|参数|
|----|------|----|
|id	    | int    |3|
|sid    | int 	 |3|
|uid    | int 	 |3|
|pid    | int 	 |3| 奖品id
|finish_time|varchat|10| //完成时间
|prize_amount | int| 4|
|dates | int| 20|
|status | int |2| 默认 0

```

**用户登录UsersController**  
```  
必传参数
	code
	rawData
	signature
	encryptedData
	iv
登录成功返回参数
	user_id
	openId
	unionId

```
**答题GuessController**

```
function getQuesstion
```

 
#!/bin/bash
mysqldump -uroot -pzyx1217110619880914 wxlist | gzip > /home/dbback/wxlist_$(date +%Y%m%d_%H%M%S).sql.gz

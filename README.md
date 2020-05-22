e-commerce data stats


    git clone https://github.com/jerrygacket/ecomdata.git
    cd ecomdata
    composer install
    -- make config/db_local.php with your db config
    php yii migrate --migrationPath=@yii/rbac/migrations
    php yii migrate
    -- setup virtual host with server_root = project_dir/web
    -- go to http://virtualhostname and you see a login form if everything is ok

demo users:

    admin 123456
    user 123456

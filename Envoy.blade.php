@servers(['staging' => 'vue-shool-user@staging-server.com'])

@setup
    $repository = 'git@github.com:LacErnest/Vue-School-Assesment.git';
    $base_dir = '/path/to/your/project';
    $release_dir = $base_dir . '/releases/' . date('YmdHis');
    $current_dir = $base_dir . '/current';
@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
    run_migrations
    update_scheduler
    optimize
    clean_old_releases
@endstory

@task('clone_repository')
    echo 'Cloning repository'
    [ -d {{ $base_dir }} ] || mkdir {{ $base_dir }}
    git clone --depth 1 {{ $repository }} {{ $release_dir }}
    cd {{ $release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release_dir }})"
    cd {{ $release_dir }}
    composer install --prefer-dist --no-scripts -q -o
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $release_dir }}/storage
    ln -nfs {{ $base_dir }}/storage {{ $release_dir }}/storage

    echo 'Linking .env file'
    ln -nfs {{ $base_dir }}/.env {{ $release_dir }}/.env

    echo 'Linking current release'
    ln -nfs {{ $release_dir }} {{ $current_dir }}
@endtask

@task('run_migrations')
    echo "Running migrations"
    php {{ $release_dir }}/artisan migrate --force
@endtask

@task('update_scheduler')
    echo "Updating scheduler"
    echo "* * * * * cd {{ $current_dir }} && php artisan schedule:run >> /dev/null 2>&1" | crontab -
@endtask

@task('optimize')
    echo "Optimizing installation"
    php {{ $release_dir }}/artisan clear-compiled
    php {{ $release_dir }}/artisan optimize
@endtask

@task('clean_old_releases')
    echo "Cleaning old releases"
    cd {{ $base_dir }}/releases
    ls -dt {{ $base_dir }}/releases/* | tail -n +4 | xargs -d "\n" rm -rf;
@endtask
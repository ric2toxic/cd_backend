var gulp = require('gulp');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var cleanCss = require('gulp-clean-css');
var path = require('path');

var swPrecache = require('sw-precache');


var paths = {
    src: './'
};


gulp.task("home_components", function(){
 
   return gulp.src("catalog/view/theme/default/javascript/home_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('home.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("common_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/common_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('common.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("list_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/list_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('list.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("product_detail_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/product_detail_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('product_detail.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("loading_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/loading_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('loading.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("login_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/login_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('login.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("layout", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/layout/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('layout.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("cart_components", function(){
	
	return gulp.src("catalog/view/theme/default/javascript/cart_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('cart.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("mobile_cart_components", function(){
	
	return gulp.src("catalog/view/theme_new_mobile/default/javascript/cart_components/*.jsx")    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('cart.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme_new_mobile/default/javascript/"));
});

gulp.task("my_account_components", function(){

  return gulp.src([
                   "catalog/view/theme/default/javascript/my_account_components/orders_components/*.jsx",
                   "catalog/view/theme/default/javascript/my_account_components/common_components/*.jsx"
                   ])
      .pipe(babel({
          presets: ['es2015', 'react']
      }))
   .pipe(concat('my_account_components.js'))
      .pipe(uglify())
      .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("account_statement_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/account_statement_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx",
                   "catalog/view/theme/default/javascript/account_statement_components/orders_components/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('account_statement.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("profile_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/profile_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('profile.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("address_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/address_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('address.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("bank_details_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/bank_details_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('bank_details.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("wishlist_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/wishlist_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('account_wishlist.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("returns_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/returns_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('returns.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task("support_components", function(){
  return gulp.src([
                   "catalog/view/theme/default/javascript/support_components/*.jsx",
                   "catalog/view/theme/default/javascript/account_menu/*.jsx"
                   ])    
       .pipe(babel({
           presets: ['es2015', 'react']
       }))
    .pipe(concat('support.js'))
       .pipe(uglify())        
       .pipe(gulp.dest("catalog/view/theme/default/javascript/"));
});

gulp.task('watch', () => {
    gulp.watch('catalog/view/theme/default/javascript/home_components/*.jsx', ['home_components']);
    gulp.watch('catalog/view/theme/default/javascript/login_components/*.jsx', ['login_components', 'react-js']);
    gulp.watch('catalog/view/theme/default/javascript/layout/*.jsx', ['layout','react-js']);
    gulp.watch('catalog/view/theme/default/javascript/loading_components/*.jsx', ['loading_components', 'react-js']);
    gulp.watch('catalog/view/theme/default/javascript/product_detail_components/*.jsx', ['product_detail_components']);
    gulp.watch('catalog/view/theme/default/javascript/list_components/*.jsx', ['list_components']);
    gulp.watch('catalog/view/theme/default/javascript/common_components/*.jsx', ['common_components', 'react-js']);

    
	gulp.watch('catalog/view/theme/default/css/*.css', ['bundle-css']);
});


//######  js concat and minified
//'catalog/view/theme/default/js/image-popoup.js',
//'catalog/view/theme/default/javascript/slick/slick.js',
gulp.task('vendor-js', function () {
	return gulp.src([ 'catalog/view/theme/default/js/jquery.js',
                    'catalog/view/javascript/jquery-ui.min.js',
				          	'catalog/view/theme/default/js/bootstrap.min.js', 
					          'catalog/view/theme/default/javascript/javascript/modernizr.2.5.3.min.js', 
					          'catalog/view/theme/default/javascript/jquery.placeholder.label.js', 
				           	'catalog/view/javascript/jquery.flagstrap.min.js',
                    'catalog/view/theme/default/javascript/sticky-kit.js',
				          	'catalog/view/theme/default/js/jq.carousel.min.js',
				          	'catalog/view/javascript/jquery-scrolltofixed-min.js', 
				          	'catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js'])
		.pipe(concat('vendor.js'))
		.pipe(uglify())
		.pipe(gulp.dest('image/js/web/'));
});


gulp.task('react-js', function () {
	return gulp.src(['catalog/view/javascript/reactjs/react.min.js', 
					'catalog/view/javascript/reactjs/react-dom.min.js', 
					'catalog/view/javascript/reactjs/polyfill.min.js', 
					'catalog/view/javascript/reactjs/axios.min.js', 
					'catalog/view/theme/default/javascript/common.js',
					'catalog/view/theme/default/javascript/login.js', 
					'catalog/view/theme/default/javascript/loading.js', 
					'catalog/view/theme/default/javascript/layout.js'])
		.pipe(concat('react-bundle.js'))
		.pipe(uglify())
		.pipe(gulp.dest('image/js/web/'));
});


//###### css minified and concat
// remove all-library.css, multiseller.css , stylesheet_new.cssfrom checkout page
//'catalog/view/theme/default/css/image-popup.css',
gulp.task('bundle-css', function () {
	return gulp.src(['catalog/view/theme/default/css/bootstrap.min.css',
					'catalog/view/javascript/jquery-ui.min.css',
					'catalog/view/theme/default/font-awesome/css/font-awesome.css',
					'catalog/view/theme/default/css/flags.css',
					'catalog/view/theme/default/css/custom.css',
					'catalog/view/theme/default/css/style.css',
					'catalog/view/theme/default/css/product-color.css',
					'catalog/view/theme/default/css/skeleton.css', 
					'catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.css',
          'catalog/view/theme/default/css/my_account.css'
					])
		.pipe(concat('stylesheet.css'))
		.pipe(cleanCss())
   .pipe(gulp.dest('image/css/web/'));
});


gulp.task('generate-sw', function() {

    swPrecache.write(path.join(paths.src, 'service-worker.js'), {

        staticFileGlobs: [
            paths.src + 'catalog/view/theme/default/javascript/blazy.min.js',
            paths.src + 'catalog/view/theme/default/javascript/product_detail.js',
            paths.src + 'catalog/view/theme/default/javascript/list.js',
            paths.src + 'catalog/view/theme/default/javascript/home.js',
            paths.src + 'catalog/view/theme/default/javascript/cart.js',
            paths.src + 'catalog/view/javascript/jquery/jquery.preload.min.js',
            paths.src + 'catalog/view/theme/default/javascript/jquery.elevatezoom.js'
        ],
        cacheId:"wsb",
        runtimeCaching: [
            {
                urlPattern: /\/api\/header\/wishlist/,
                handler: 'networkFirst',
                options: {cache: { maxEntries: 50, name:'short-cache', maxAgeSeconds: 300 }}
            },
            {
                urlPattern: /\/api\/header\/menu[^\s]/,
                handler: 'cacheFirst',
                options: {cache: { maxEntries: 50, name:'medium-cache', maxAgeSeconds: 3600 }}
            },
            {
                urlPattern: /\/api\/home\/*.*/,
                handler: 'cacheFirst',
                options: {cache: { maxEntries: 50, name:'medium-cache', maxAgeSeconds: 3600 }}
            },
            {
                urlPattern: /\/api\/login\/*.*/,
                handler: 'cacheFirst',
                options: {cache: { maxEntries: 100, name:'long-cache',maxAgeSeconds: 86400 }}
            },
            {
                urlPattern: /\/api\/footer\/*.*/,
                handler: 'cacheFirst',
                options: {cache: { maxEntries: 100, name:'long-cache', maxAgeSeconds: 86400}}
            },
            {
                urlPattern: /\/api\/header\/logo/,
                handler: 'cacheFirst',
                options: {cache: { maxEntries: 100, name:'long-cache', maxAgeSeconds: 86400}}
            },
            {
                urlPattern: /\/*.*\/web\/*.*/,
                handler: 'cacheFirst',
                options: {origin: 'https://cdnimages.net/', cache: { maxEntries: 100, name:'long-cache',maxAgeSeconds: 86400 }}
            },
            {
                urlPattern: /\/css\/*.*/,
                handler: 'cacheFirst',
                options: {origin: 'https://cdnimages.net/', cache: { maxEntries: 100, name:'long-cache',maxAgeSeconds: 86400 }}
            }

        ],

        stripPrefix: paths.src,
        verbose: false
    });

});


gulp.task('default', ['home_components', 'common_components', 'list_components', 'product_detail_components', 'loading_components', 'login_components', 'layout', 'cart_components', 'mobile_cart_components', 'my_account_components', 'account_statement_components']);

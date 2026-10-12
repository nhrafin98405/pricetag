<!doctype html>
<html lang='en'>
<head>
    @include("layouts.admin.head")
</head>
<body class='bg-theme bg-theme9'>
    <div class='wrapper'>
        @include("layouts.admin.sidebar")
        @include("layouts.admin.header")
        <main>
            @yield("content")
        </main>
        @include("layouts.admin.footer")
        @include("layouts.admin.theme-switcher")
    </div>
    @include("layouts.admin.scripts")
</body>
</html>

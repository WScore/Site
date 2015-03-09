WScore/Site
===========

ウェブサイト構築のためのユティリティクラスの詰め合わせ。

### License

MIT License

### Tools

*   Files: ファイル操作用ツール。
*   Swift: SwiftMailer用ツール。

Files
-----

ファイル操作を簡易的に行う。Php LeagueのFlysystemを利用することが多い。

### FlyCsv

CSVファイル読込用クラス。SplFileを継承したクラス。SJISのCSVを簡単に操作できる。

サンプルコード：

```php
$csv = FlyCsv::openSJis('filename.csv');
$csv->readHeader();
$csv->setMap([
    'ユーザーID' => 'user_id',
    '氏名'       => 'user_name',
]);
foraech($csv as $data) {
    var_dump($data);
}
```

##### construct or openSJis

newすれば、UTF-8のCSVファイルとして扱う。

SJISのCSVを扱うには```openSJis```メソッドでファイルを開く。
文字コードを変換したテンポラリファイルを作成して、そのポインターを返す。

```php
$csv = FileCsv::openSJis($file, $encode);
```

*   $file: 変換前のファイルパス、あるいはファイルポインター。
*   $encode: 変換前の文字コード。デフォルトは'SJIS-win'。


##### readHeader()メソッド

読み込んだ行をヘッダーとして認識する。
ヘッダーを読み込んだら、ヘッダーの連想配列としてデータを返す。

##### setMap()メソッド

データを更に別の連想配列キーに変換する。
例えばデータベーステーブルの項目名で設定された連想配列としてCSVを取得したい場合に使う。


### FlyUpload

ファイルアップロード用。

##### [_] まだ見なおす必要がありそう。ファイルパスが取得できないかも。

```html
<form name='up' method='post' action='up.php' enctype="multipart/form-data" >
<input type='file' name='upload_file' />
</form>
```

こんなHTMLの場合、PHPファイルでアップロードファイルを操作するには…

```php
try {

    $upload = FlyUpload::open('upload_file');
    $upload->local('/path/to/local/storage');
    $upload->moveToFile('file-name.ext');
    if($upload->fails()) {
        throw new \RuntimeException('upload failed', $upload->getErrorCode());
    }
    $fp  = $upload->open();
    $csv = FlyCsv::openSJis($fp);

} catch(\RuntimeException $e) {
    $errorCode = $e->getCode();
    $message   = $e->getMessage();
}
```


### FlySystems

Flysystemの種々のファイルシステムを作成するヘルパー。

```php
$local = FlySystems::local('/path/to/storage');
$null  = FlySystems::null();
$ftp   = FlySystems::ftp(
    'ftp.example.com',
    'user-name', 
    'pass-word', 
    '/root', 
    [ 'port'=>21, 'passive'=>true, 'ssl'=>true, 'timeout'=>10 ]
);
```


### FlySync

Flysystemを使ったファイルの同期。

```php
// set up sync
$sync = FlySync(
    FlySystems::ftp('from.example.com', 'user'...);
    FlySystems::local('/path/to/storage/');
);

// synchronize all files under directory.
$sync->syncDir('/path/data/dir/');

// synchronize specified files. 
$sync->syncFiles([
    'file-name1.ext',
    'file-name2.ext',
]);
```

Swift
-----

SwiftMailer用。LaravelのMailerと同じようなAPI。

```php
$mailer = MailerFactory::forgeSmtp('loaclhost');
$mailer->sendText( 'mail body text', function($message) {
    /** @var \Swift_Message $message */
    $message->setTo('to@mail.com', 'send-to name');
    $message->setSubject('mail subject');
    $message->addFrom('from@mail.com', 'from name');
    $message->setBcc('bcc1@mail.com', 'bcc2@mail.com');
});
```

クロージャー使っているけれど、やっているのは下記と同じコード。

```php
$message = $mailer->message();
// messageを組み立てる。
$message->setTo()...
$mailer->getMailer()->send($message);
```

*   sendText:テキストを送信。
*   sendHtml:HTML添付として送信。
*   sendJis:下記参照のこと。

##### デフォルト設定

ウェブサイト共通のメール送信でのデフォルトを設定する。

```php
$default = new \WScore\Site\Swift\MessageDefault();
$default
    ->setFrom('from1@example.com', 'from name#1')
    ->setFrom('from2@example.com', 'from name#2')
    ->setReplyTo('reply@example.com', 'reply to name')
    ->setReturnPath('return@example.com');

$mailer
    ->setDefault($default)
    ->sendHtml( '<html><h1>HTMLです</h1></html>', function($message) {
        /** @var \Swift_Message $message */
        $message->setTo('to@mail.com', 'send-to name');
    });
```

> 文面フッターなどの共通化はテンプレートを用いること。


### 日本語メール（ISO2022-JIS）

最初に```MailerFactory::goJapaneseIso2022();```を呼び出しておく。

```php
MailerFactory::goJapaneseIso2022();
$mailer = MailerFactory::forgeSmtp('loaclhost');

$mailer->sendJis( '日本語JISです', function($message) {
    /** @var \Swift_Message $message */
    $message->setTo('to@mail.com', 'send-to name');
});
```

### Mailerとプラグイン

Mailer今のところ、これだけ。

```php
MailerFactory::forgeSmtp($host='localhost', $port=25, $security = null, $user=null, $pass=null);
```

```php
MailerFactory::forgeNull();
```

```php
MailerFactory::forgeFileSpool('/path/to/mail/spool/');
```

```php
MailerFactory::forgePhpMailer();
```

##### antiFloodプラグイン

```php
$mailer = MailerFactory::forgeSmtp();
MailerFactory::antiFlood($mailer, 99, 1); // 99通送ったら1秒休む
```

##### throttleプラグイン

```php
$mailer = MailerFactory::forgeSmtp();
MailerFactory::throttle($mailer, 10); // 一分に10通まで。
```

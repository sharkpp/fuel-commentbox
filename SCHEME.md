# 仕様

## 目標

ページにコメント欄を追加できるパッケージを作る。

## ToDo

* リプライ

## 機能

* <del>Auth パッケージと連携</del>
* ツリー形式のコメント
	* [Isso – a commenting server similar to Disqus](http://posativ.org/isso/)
	* [java - javafx.application.applicationを拡張する必要がありますというエラーがでました - スタック・オーバーフロー](http://ja.stackoverflow.com/questions/6461/javafx-application-application%e3%82%92%e6%8b%a1%e5%bc%b5%e3%81%99%e3%82%8b%e5%bf%85%e8%a6%81%e3%81%8c%e3%81%82%e3%82%8a%e3%81%be%e3%81%99%e3%81%a8%e3%81%84%e3%81%86%e3%82%a8%e3%83%a9%e3%83%bc%e3%81%8c%e3%81%a7%e3%81%be%e3%81%97%e3%81%9f)
	* などを参考
* コメントのモデレート(+/-)
* <del>テンプレート機能</del>
* マイグレーション処理
	* <del>設定でテーブル名を変更できるように</del>
* <del>テスト用のページを追加</del>
* <del>コメントのルートをどうする？</del>
	* <del>コメントと表示するページへの割当を別のテーブルにしてそこで関連づける</del>
	* <del>複数の種類のページがある場合は？</del>
		* <del>静的なマイグレーションだとダメそう</del>
		* <del>マイグレーションを作るリファインがいる？</del>
* コメントフォーム
	* <del>名前表示</del>
		* <del>ログイン時は Auth パッケージから引っ張ってくる</del>
	* アイコン表示
		* <del>[Gravatar - Globally Recognized Avatars](https://en.gravatar.com/) でアイコン表示</del>
		* <del>[RoboHash](http://robohash.org/) でアイコン表示</del>
		* [Identicon](http://www.radiumsoftware.com/0702.html)
	* いつ投稿したか
		* <del>`an hour ago` 的な表示</del>
	* メッセージ本文
		* プレーンテキスト
		* MarkDownでかけるように？
			* HTMLタグをエスケープしないと、、、
		* wysiwyg
* <del>リプライ機能</del>
* [recaptcha](http://www.google.com/recaptcha/intro/index.html) で CAPTCHA 表示

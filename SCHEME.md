# 仕様

## 目標

ページにコメント欄を追加できるパッケージを作る。

## ToDo

* <del>ページに埋め込み</del>
* <del>コメントできる</del>

## 機能

* Auth パッケージと連携
* ツリー形式のコメント
	* [Isso – a commenting server similar to Disqus](http://posativ.org/isso/)
	* [java - javafx.application.applicationを拡張する必要がありますというエラーがでました - スタック・オーバーフロー](http://ja.stackoverflow.com/questions/6461/javafx-application-application%e3%82%92%e6%8b%a1%e5%bc%b5%e3%81%99%e3%82%8b%e5%bf%85%e8%a6%81%e3%81%8c%e3%81%82%e3%82%8a%e3%81%be%e3%81%99%e3%81%a8%e3%81%84%e3%81%86%e3%82%a8%e3%83%a9%e3%83%bc%e3%81%8c%e3%81%a7%e3%81%be%e3%81%97%e3%81%9f)
	* などを参考
* コメントのモデレート(+/-)
* テンプレート機能
* マイグレーション処理
	* 設定でテーブル名を変更できるように
* テスト用のページを追加
* コメントのルートをどうする？
	* コメントと表示するページへの割当を別のテーブルにしてそこで関連づける
	* 複数の種類のページがある場合は？
		* 静的なマイグレーションだとダメそう
		* マイグレーションを作るリファインがいる？
* コメントフォーム
	* 名前表示
		* ログイン時は Auth パッケージから引っ張ってくる
	* アイコン表示
	* いつ投稿したか
		* `an hour ago` 的な表示
	* メッセージ本文
		* プレーンテキスト
		* MarkDownでかけるように？
			* HTMLタグをエスケープしないと、、、
		* wysiwyg
* リプライ機能
* [recaptcha](http://www.google.com/recaptcha/intro/index.html) で CAPTCHA 表示
* [Gravatar - Globally Recognized Avatars](https://en.gravatar.com/) でアイコン表示
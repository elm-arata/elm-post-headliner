# ELM Headliner ~ WordPress Plugin

記事ヘッドラインを表示するためのWordPressプラグイン。


## Simple Usage

最新の投稿を5件表示する。

    [headliner]

## Advanced Usage

### カテゴリースラッグ *hoge* の投稿を10件表示する

    [headliner category_name=hoge posts_per_page=10]

### コンテナ要素に任意の id や class を付与

    [headliner id=hoge class=piyo]

複数のクラスをつける場合はクォーテーションで括る

    [headliner id=hoge class="piyo foo bar"]

特定のヘッドラインにスタイルを指定したい時は、コンテナにid（またはclass）を付与し、それをセレクタに使う。
特定のヘッドラインにのみカスタムテンプレートを適用する場合にも、コンテナにid（またはclass）を付与することが有効。

## Options

※オプション利用は全て任意

### Query option

* `author` *(string|int)* - 投稿者ID。カンマ区切りで複数指定可。
* `post_type` *(string)* - 検索対象の投稿タイプ。デフォルトは 'post'。
	* 'post' - 投稿
	* 'page' - 固定ページ
	* 'any' - 全ての投稿タイプ（リビジョンおよび検索禁止の投稿タイプを除く）
	* カスタム投稿タイプのスラッグ
* `category_name` *(string)* - 検索対象のカテゴリースラッグ。カンマ区切りで複数指定可。
* `posts_per_page` *(int)* - 表示件数。デフォルトは5件。
* `offset` *(int)* - 表示オフセット。指定件数ずらして投稿を取得する。デフォルトは0（オフセット無し）。
* `order` *(string)* - 並び順の指定。'ASC'(昇順) or 'DESC'（降順）。デフォルトは降順。
* `orderby` *(string)* - 何で並び替えるか。デフォルトは投稿日付。  スペース区切りで複数指定可（その場合はクォーテーションで括る）。
	* 'date' - 投稿日付（これがデフォルト）
	* 'modified' - 更新日付
	* 'author' - 投稿者
	* 'title' - タイトル
	* 'name' - スラッグ
	* 'ID' - 投稿ID
	* 'menu_order' - 指定した並び順。（固定ページにある、管理画面で指定する並び順のこと。）
	* 'rand' - ランダム！！
	* その他、WP_Query で使える orderby パラメータ全般。一部非対応。
* `ignore_sticky_posts` *(boolean)* - 先頭固定を無視する。デフォルトは false （無視しない）。
* `query` *(string)* - クエリ文字列を直接指定できる。**このオプションを使った場合、他のあらゆるクエリーオプションは無視される**。使わずに済めばその方がいい。

### Query option (taxonomy)

* `tax` *(string)* - 検索対象のタクソノミースラッグ。
* `tax_field` *(string)* - `tax`で指定したタクソノミーでのターム検索方式。('id' or 'slug')
* `tax_terms` *(int, string)* - `tax_field`の検索パラメータ。カンマ区切りで複数指定可。
* `tax_include_children` *(boolean)* - 階層型タクソノミーの場合、子タームも検索に含めるかどうか。デフォルトは true。

### Output option

* `id` *(string)* - コンテナ要素のid属性。デフォルトは無し。
* `class` *(string)* - コンテナ要素のclass属性。デフォルトは 'headliner-container'。
* `container_tag` *(string)* - コンテナ要素に利用するHTMLタグ。デフォルトは 'ul'。
* `item_tag` *(string)* - ヘッドラインアイテム要素のHTMLタグ。デフォルトは 'li'。
* `item_class` *(string)* - ヘッドラインアイテム要素のclass属性。デフォルトは 'headliner-item'。
* `date_format` *(string)* - 日付表示フォーマット。PHPに準拠。デフォルトは 'Y/m/d'。
* `thumbnail` *(string)* - 記事のアイキャッチ画像のサムネイルを出力するかどうか。デフォルトは非表示 'none'。('show' or 'none')
* `size` *(string)* - サムネイルを出力する場合、サイズの指定。デフォルトは 'thumbnail'。
* `no_image` *(string)* - `thumbnail=show` の時に、サムネイル画像が存在しなかった場合の代替画像URL。
* `excerpt` *(string)* - 記事の概要テキストを出力するかどうか。デフォルトは非表示 'none'。('show' or 'none')
* `new_label_days` *(int)* - 投稿日が指定日数以内であれば「New」ラベルを表示。デフォルトは非表示 ( 0 )。


## フィルターフック

### elm-post-headliner-template

ヘッドラインアイテムのカスタムテンプレートを使うことができる。

```
// 例1
function my_eph_template() {
	$html = <<< EOD
<div class="my-eph-item">
	%post_thumbnail%
	<a href="%post_url%">%post_title%</a>
	<span class="item-category item-category-%category_nicename%">%category_name%</span>
</div>
EOD;
	return $html;
}
add_filter('elm-post-headliner-template', 'my_eph_template');

// 例2
// ショートコードを複数使う場合に
// 特定のもののみカスタムテンプレートにするには
// ショートコードオプション `id` を指定した上で （ [headliner id=nanika] ）
// 以下のようにする。（もちろん class指定でも同じようなことは可能。）
function my_eph_template_for_nanika($html, $params) {
	// id 指定が 「nanika」の場合のみカスタムテンプレートを適用
	if ($params['id'] == 'nanika') {
		$html = <<< EOD
<div class="my-eph-item-nanika">
	%post_thumbnail%
	<a href="%post_url%">%post_title%</a>
	<span class="item-category item-category-%category_nicename%">%category_name%</span>
</div>
EOD;
	}
	return $html;
}
add_filter('elm-post-headliner-template', 'my_eph_template_for_nanika', 10, 2);
```

ヘッドラインアイテムテンプレート内で利用できる置換タグは以下のとおり。

* `%post_date%` : 投稿日時。フォーマットはオプションパラメータ `date_format` で指定できる。
* `%post_url%` : 投稿のパーマリンク。
* `%post_title%` : 投稿のタイトル。
* `%post_thumbnail%` : 投稿のアイキャッチ画像。投稿へのリンク付きimgタグとして出力される（例：<a href="hoge.html"><img src="piyo.jpg"></a>）。サムネイル画像が無い場合でオプション `no_image` 指定があれば、それをURLとしたHTMLとして出力される。
* `%post_thumbnail_url%` : 投稿のアイキャッチ画像URL。`%post_thumbnail%` と異なり、URLのみが出力される。サムネイル画像が無い場合でオプション `no_image` 指定があればそれが出力される。
* `%post_excerpt%` : 投稿の概要。
* `%category_name%` : 投稿のカテゴリー名。（複数カテゴリーに属する投稿であっても、１つめのカテゴリーのみ。）
* `%category_nicename%` : 投稿のカテゴリースラッグ。（複数カテゴリーに属する投稿であっても、１つめのカテゴリーのみ。）
* `%author%` : 投稿者名。ユーザープロフィールにおける「ブログ上の表示名」が用いられる。**※管理画面で投稿者の表示名を明示的に設定していない場合、ログインIDが使われてしまうことに注意**。これはWordPressの仕様。
* `%author_link%` : 投稿者のウェブサイトへのリンク。リンクテキストは投稿者名。**※管理画面で投稿者の表示名を明示的に設定していない場合、ログインIDが使われてしまうことに注意**。これはWordPressの仕様。
* `%author_meta.hoge%` : 投稿者メタ情報。`hoge` 部分はメタ情報のフィールド名を入れる。詳細は[get_the_author_metaのドキュメント](http://codex.wordpress.org/Function_Reference/get_the_author_meta)を参照。

後述するフィルター `elm-post-headliner-textreplace` を定義すれば、ここで挙げた置換タグ以外に独自の置換タグを定義することもできる。（当然ながら独自の置換タグには、置換処理自体も自分で書く必要がある）


### elm-post-headliner-textreplace

ヘッドラインアイテム要素の置換処理に独自の置換処理を加えることができる。置換後のテキストを返すこと。

```
// 例
function my_eph_replace($text, $params, $post) {
	// 独自タグ %custom_replace_tag% の置換処理
	$text = preg_replace('/%custom_replace_tag%/', '置換後のテキスト', $text);

	// 既存タグの置換処理を乗っ取るのことも可。こちらの処理が優先される。
	// $text = preg_replace('/%post_title%/', 'あいうえお', $text);

	return $test;
}
add_filter('elm-post-headliner-textreplace', 'my_eph_replace', 10, 3);
```

特定のもののみ独自置換処理を施すには、フィルターフック `elm-post-headliner-template` の例2を参照。


### elm-post-headliner-new-label

「New」ラベルのhtmlを任意のものに変更できる。ラベルのHTMLを返す。

```
// 例
function my_eph_new_label($label_html) {
	// $label_html に元のラベルHTMLが格納されている

	return '<span class="my_custom_label_hogehoge">NEW!!</span>';
}
add_filter('elm-post-headliner-new-label', 'my_eph_new_label', 10, 1);
```

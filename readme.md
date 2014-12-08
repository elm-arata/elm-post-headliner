# ELM Headliner ~ WordPress Plugin

記事ヘッドラインを表示するためのWordPressプラグイン。


## Simple Usage

    [headliner]

## Advanced Usage

### カテゴリースラッグ *hoge* の投稿を10件表示する

    [headliner category_name=hoge posts_per_page=10]

### コンテナ要素に任意の id を付与

    [headliner container_id=hoge]

特定のヘッドラインにスタイルを指定したい時は、コンテナにid（またはclass）を付与し、それをセレクタに使う。

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
* `query` *(string)* - クエリ文字列を直接指定できる。このオプションを使った場合、他のあらゆるクエリーオプションは無視される。

### Query option (taxonomy)

* `tax` *(string)* - 検索対象のタクソノミースラッグ。
* `tax_field` *(string)* - `tax`で指定したタクソノミーでのターム検索方式。('id' or 'slug')
* `tax_terms` *(int, string)* - `tax_field`の検索パラメータ。カンマ区切りで複数指定可。
* `tax_include_children` *(boolean)* - 階層型タクソノミーの場合、子タームも検索に含めるかどうか。デフォルトは true。

### Output option

* `container_tag` *(string)* - コンテナ要素に利用するHTMLタグ。デフォルトは 'ul'。
* `container_id` *(string)* - コンテナ要素のid属性。デフォルトは無し。
* `container_class` *(string)* - コンテナ要素のclass属性。デフォルトは 'headliner-container'。
* `item_tag` *(string)* - ヘッドラインアイテム要素のHTMLタグ。デフォルトは 'li'。
* `item_class` *(string)* - ヘッドラインアイテム要素のclass属性。デフォルトは 'headliner-item'。
* `date_format` *(string)* - 日付表示フォーマット。PHPに準拠。デフォルトは 'Y/m/d'。
* `thumbnail` *(string)* - 記事のアイキャッチ画像のサムネイルを出力するかどうか。デフォルトは非表示 'none'。('show' or 'none')
* `size` *(string)* - サムネイルを出力する場合、サイズの指定。デフォルトは 'thumbnail'。
* `excerpt` *(string)* - 記事の概要テキストを出力するかどうか。デフォルトは非表示 'none'。('show' or 'none')


## フィルターフック

### elm-post-headliner-template

ヘッドラインアイテムのカスタムテンプレートを使うことができる。

```
// 例
function my_eph_template() {
	return '<div>%post_title%</div>';
}
add_filter('elm-post-headliner-template', 'my_eph_template');
```

ヘッドラインアイテムテンプレート内で利用できる置換タグは以下のとおり。

* `%post_date%` : 投稿日時。フォーマットはオプションパラメータ `date_format` で指定できる。
* `%post_url%` : 投稿のパーマリンク。
* `%post_title%` : 投稿のタイトル。
* `%post_thumbnail%` : 投稿のアイキャッチ画像。投稿へのリンク付きimgタグとして出力される。（例：<a href="hoge.html"><img src="piyo.jpg"></a>）
* `%post_excerpt%` : 投稿の概要。
* `%category_name%` : 投稿のカテゴリー名。（複数カテゴリーに属する投稿であっても、１つめのカテゴリーのみ。）
* `%category_nicename%` : 投稿のカテゴリースラッグ。（複数カテゴリーに属する投稿であっても、１つめのカテゴリーのみ。）

後述するフィルター `elm-post-headliner-textreplace` を定義すれば、ここで挙げた置換タグ以外に独自の置換タグを定義することもできる。（当然ながら独自の置換タグには、置換処理自体も自分で書く必要がある）


### elm-post-headliner-textreplace

ヘッドラインアイテム要素の置換処理に独自の置換処理を加えることができる。置換後のテキストを返すこと。

```
// 例
function my_eph_replace($text, $options, $post) {
	//独自タグの置換処理
	return preg_replace('/%custom_replace_tag%/', '独自タグを置き換えるテキスト', $text);

	//既存タグの置換処理を乗っ取るのも可
	// return preg_replace('/%post_title%/', 'あいうえお', $text);
}
add_filter('elm-post-headliner-textreplace', 'my_eph_replace', 10, 3);
```

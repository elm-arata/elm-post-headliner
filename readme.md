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

* `post_type` *(string)* - 検索対象の投稿タイプ。デフォルトは 'post'。
	* 'post' - 投稿
	* 'page' - 固定ページ
	* 'any' - 全ての投稿タイプ（リビジョンおよび検索禁止の投稿タイプを除く）
	* カスタム投稿タイプのスラッグ
* `category_name` *(string)* - 検索対象のカテゴリースラッグ。カンマ区切りで複数指定可。
* `posts_per_page` *(int)* - 表示件数。デフォルトは5件。
* `tax` *(string)* - 検索対象のタクソノミースラッグ。
* `tax_field` *(string)* - `tax`で指定したタクソノミーでのターム検索方式。('id' or 'slug')
* `tax_terms` *(int, string)* - `tax_field`の検索パラメータ。カンマ区切りで複数指定可。
* `tax_include_children` *(boolean)* - 階層型タクソノミーの場合、子タームも検索に含めるかどうか。デフォルトは true。
* `query` *(string)* - クエリ文字列を直接指定できる。このオプションを使った場合、他の全てのクエリーオプションは無視される。

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

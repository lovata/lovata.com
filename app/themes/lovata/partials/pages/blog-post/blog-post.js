export default new class BlogPost {
  constructor() {
    this.blogPostPageSelector = '[data-blog-post-page]';

    if(!$(this.blogPostPageSelector).length) return;

    this.wrapTable();
  }

  wrapTable() {
    $('table').wrap('<div class="post--table-wrap"></div>');
  }
}

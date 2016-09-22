var MaQmaTickets = {};

MaQmaTickets.Podcast = function() {
    this.title = 'Test title';
    this.description = 'A description';
    this.link = 'http://www.imaqma.com';
};

MaQmaTickets.Podcast.prototype.toString = function() {
    return 'Title: ' + this.title;
};
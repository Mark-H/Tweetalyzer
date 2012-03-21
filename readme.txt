++  Tweetalyzer
++  Developer:  Mark Hamstra
++  License:    GPL GNU v2
+++++++++++++++++++++++++++++++++++

Tweetalyzer uses the public Twitter Search API to fetch tweet with your search,
analyzes them with a customied version of James Hennessey's phpInsight Sentiment
Analysis class and displays the tweet with a funny emoticon.

Not convinced you need this on your site? Boo-hiss! Ryan made me do it!

Bugs & Features: 	https://github.com/Mark-H/Tweetalyzer/issues

Tweetalyzer Snippet Usage:

Simply put it on your site with the [[Tweetalyzer]] snippet call. Cached or getCache suggested.
Available snippet properties:

- &search (#modx) the search to use on Twitter
- &amount (10) number of tweets to collect
- &registerCss (true) register the very basic CSS for very basic default styling
- &tweetTpl (tweetalyzerTweet) a tpl chunk for outputting each individual tweet.
- &outerTpl (tweetalyzerOuter) a tpl chunk for the outer output.
- &tweetSeparator ('') a string to join individual tweets with.

phpInsight Sentiment Analysis by James Hennessey released under the GPL: https://github.com/JWHennessey/phpInsight

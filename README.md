# SkelPHP Header

*NOTE: The Skel framework is an __experimental__ web applications framework that I've created as an exercise in various systems design concepts. While I do intend to use it regularly on personal projects, it was not necessarily intended to be a "production" framework, since I don't ever plan on providing extensive technical support (though I do plan on providing extensive documentation). It should be considered a thought experiment and it should be used at your own risk. Read more about its conceptual foundations at [my website](https://colors.kaelshipman.me/about/this-website).*

This is the Skel Header package. It is intended to be the only real dependency for most other Skel packages<sup>[1](#ftnt1)</sup>.

It is HIGHLY INCOMPLETE in its current state. I started off defining certain interactions, but they changed so quickly and drastically as I was developing that I decided to formalize component interactions after the fact and have left most of the interfaces as simple placeholders. This does seem a little backwards, but it turned out that there were many more real-world challenges in defining ideal interactions than I had anticipated (go figure).

As the framework's first viable beta version nears completion, I'll be returning to these to flesh them out and document them.

## Usage

Eventually, this package will form the foundation of the Skel framework. It is intended to be loaded as a composer package (not yet -- but eventually) and constrain the implementation of your personal application framework. The point of it is to allow authors to build their own personal frameworks based on their own logic, but in a way that's interoperable with components built by other authors. This should theoretically allow us to compose our applications of pieces that we know intimately (because we built them ourselves) and pieces that are more professionally acceptable (because they're large and widely supported), and all without losing the benefits of a framework.

Because this is still in very active development, I currently use it via a git submodule:

```bash
cd ~/my-website
git submodule add git@github.com:kael-shipman/skelphp-header.git app/dev-src/skelphp/header
```

This allows me to develop it together with the website I'm building with it. For more on the (somewhat awkward and complex) concept of git submodules, see [this page](https://git-scm.com/book/en/v2/Git-Tools-Submodules).


## Notes

<a name="ftnt1">[1]</a>: The Skel framework was designed in response to dependency nightmares and bloat. Thus, it was intended to be a set of interfaces that anyone could use to create interoperable framework components with their own logic. Read more [here](https://colors.kaelshipman.me/about/this-website).

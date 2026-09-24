# Contributing

Contributions are **welcome** and will be fully **credited**.

Please read and understand the contribution guide before creating an issue or pull request.

## Etiquette

The maintainers give their time to build and maintain this code and make it available in the hope that it will be useful. Please be considerate towards them when raising issues or presenting pull requests.

## Procedure

Before filing an issue:

- Attempt to replicate the problem, to ensure that it wasn't a coincidental incident.
- Check to make sure your feature suggestion isn't already present within the project.
- Check the pull requests tab to ensure that the bug doesn't have a fix in progress.
- Check the pull requests tab to ensure that the feature isn't already in progress.

Before submitting a pull request:

- Check the codebase to ensure that your feature doesn't already exist.
- Check the pull requests to ensure that another person hasn't already submitted the feature or fix.

## Requirements

- **Run the checks locally** — `composer ci` runs what CI runs: composer.json normalization, Rector, Pint, PHPStan and the test suite. `composer cs` fixes what can be fixed automatically.
- **Add tests!** — Your patch will not be accepted if it does not have tests.
- **Title the PR in [Conventional Commits](https://www.conventionalcommits.org/) form** — e.g. `feat: add X` or `fix(tables): Y`. PRs are squash-merged, so the title becomes the commit message and the line in the release notes.
- **Document any change in behaviour** — Make sure the `README.md` and any other relevant documentation are kept up-to-date. Do not edit `CHANGELOG.md`; it is written from the release notes.
- **Consider our release cycle** — We follow [SemVer v2.0.0](https://semver.org/). Randomly breaking public APIs is not an option.
- **One pull request per feature** — If you want to do more than one thing, send multiple pull requests.

**Happy coding**!

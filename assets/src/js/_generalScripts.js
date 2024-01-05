
class General {
	constructor() {
		this.testVariable = 'script working';
		this.init();
		this.check();
	}

	init() {
		// for tests purposes only
		console.log(this.testVariable);
	}

	check() {
		console.log('check');
	}
}

export default General;

export default {
	methods: {
			/**
			*	wether or not the input contains only spaces/line-breakers
			*	@return boolean
			**/
			isEmpty (input)
			{
				return input.trim().length == 0
			},

			/**
			*	wether or not user hit the Shift + Enter on keyboard
			*	@return boolean
			**/
			shiftPlusEnter (event)
			{
				return event.shiftKey
			}
		}
};
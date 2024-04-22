import { Ability } from '@casl/ability'
import { initialAbility } from './config'

//  Read ability from localStorage
// * Handles auto fetching previous abilities if already logged in user
// ? You can update this if you store user abilities to more secure place
// ! Anyone can update localStorage so be careful and please update this
const _ac = localStorage.getItem('_ac')
const existingAbility = _ac ? initialAbility.manage : initialAbility.auth

export default new Ability(existingAbility)
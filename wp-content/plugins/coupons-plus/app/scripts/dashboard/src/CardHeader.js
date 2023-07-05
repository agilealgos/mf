import React, {Component} from 'react';
import { connect } from "react-redux";
import CardIcon from './CardIcon';
import classNames from 'classnames';

class CardHeader extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        const dark = (typeof this.props.dark === 'boolean') && this.props.dark;

        return (
            <div className="flex flex-row items-center justify-between space-x-4">
                <div className="flex flex-row space-x-2 items-center">
                    <CardIcon icon={this.props.card.getIcon()} colors={this.props.iconColors} />
                    <h1 className={classNames({
                        'text-1x': true,
                        'text-gray-800': !this.props.titleColor,
                        [this.props.titleColor || '']: this.props.titleColor
                    })}>{this.props.card.getTitle()}</h1>
                </div>
                <button 
                    className={classNames({
                        'text-transparent h-5 px-3 rounded-full': true,
                        'bg-gray-150 hover:bg-gray-350 hover:text-gray-100': !this.props.buttonColors,
                        [this.props.buttonColors || '']: this.props.buttonColors
                    })}
                    onClick={this.props.onClose}
                >
                    {__('remove', CouponsPlus.textDomain)}
                </button>
            </div>
        );
    }
}

export default connect(CardHeader.mapStateToProps, {})(CardHeader);
import React, {Component} from 'react';
import { connect } from "react-redux";
import classNames from 'classnames';

class CardIcon extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className={classNames({
                'flex rounded-5 p-1': true,
                'bg-gray-350': !this.props.colors,
                [this.props.colors || '']: this.props.colors
            })}>
                <div className={`children:w-4 children:h-4 text-gray-100`}>{this.props.icon}</div>
            </div>
        );
    }
}

export default connect(CardIcon.mapStateToProps, {})(CardIcon);
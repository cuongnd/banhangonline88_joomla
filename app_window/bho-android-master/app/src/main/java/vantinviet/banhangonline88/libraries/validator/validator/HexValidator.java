package vantinviet.banhangonline88.libraries.validator.validator;

import android.content.Context;
import android.graphics.drawable.Drawable;

import com.vantinviet.vtv.R;
import com.vantinviet.vtv.libraries.validator.AbstractValidator;

import java.util.regex.Pattern;


public class HexValidator extends AbstractValidator {

    private static final Pattern HEX_PATTERN = Pattern.compile("^(#|)[0-9A-Fa-f]+$");
    private static final int DEFAULT_ERROR_MESSAGE_RESOURCE = R.string.validator_hex;

    public HexValidator(Context c) {
        super(c, DEFAULT_ERROR_MESSAGE_RESOURCE);
    }

    public HexValidator(Context c, int errorMessageRes) {
        super(c, errorMessageRes);
    }

    public HexValidator(Context c, int errorMessageRes, Drawable errorDrawable) {
        super(c, errorMessageRes, errorDrawable);
    }

  @Override
    public boolean isValid(String text) {
        return HEX_PATTERN.matcher(text).matches();
    }
}

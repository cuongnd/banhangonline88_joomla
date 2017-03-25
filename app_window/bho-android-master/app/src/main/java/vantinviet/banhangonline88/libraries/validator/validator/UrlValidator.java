package vantinviet.banhangonline88.libraries.validator.validator;

import android.content.Context;
import android.graphics.drawable.Drawable;
import android.util.Patterns;

import com.vantinviet.vtv.R;
import com.vantinviet.vtv.libraries.validator.AbstractValidator;

import java.util.regex.Pattern;


public class UrlValidator extends AbstractValidator {

    private static final Pattern WEB_URL_PATTERN = Patterns.WEB_URL;
    private static final int DEFAULT_ERROR_MESSAGE_RESOURCE = R.string.validator_url;

    public UrlValidator(Context c) {
        super(c, DEFAULT_ERROR_MESSAGE_RESOURCE);
    }

    public UrlValidator(Context c, int errorMessageRes) {
        super(c, errorMessageRes);
    }

    public UrlValidator(Context c, int errorMessageRes, Drawable errorDrawable) {
        super(c, errorMessageRes, errorDrawable);
    }

    @Override
    public boolean isValid(String url) {
        return WEB_URL_PATTERN.matcher(url).matches();
    }
}
